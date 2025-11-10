<?php

namespace App\Legacy\Transformer\Ecs;

use App\Domain\Ecs\Systeme\Reseau\{BouclageReseau, IsolationReseau};
use App\Dto\Ecs\Systeme\{ReseauDto, StockageDto, SystemeDto};
use App\Legacy\Model\{GenerateurEcs, InstallationEcs};
use App\Legacy\Transformer\Context;

final class SystemeTransformer
{
    private InstallationEcs $installation_ecs;
    private GenerateurEcs $generateur_ecs;

    public function alimentation_contigues(): bool
    {
        return match ($this->installation_ecs->tv_rendement_distribution_ecs_id) {
            1, 4, 6 => true,
            default => false,
        };
    }

    public function niveaux_desservis(): int
    {
        return $this->installation_ecs->nombre_niveau_installation_ecs > 0
            ? $this->installation_ecs->nombre_niveau_installation_ecs
            : 1;
    }

    public function isolation_reseau(): ?IsolationReseau
    {
        return match ($this->installation_ecs->reseau_distribution_isole) {
            true => IsolationReseau::ISOLE,
            false => IsolationReseau::NON_ISOLE,
            default => null,
        };
    }

    public function bouclage_reseau(): ?BouclageReseau
    {
        return match ($this->installation_ecs->enum_bouclage_reseau_ecs_id) {
            1 => BouclageReseau::RESEAU_NON_BOUCLE,
            2 => BouclageReseau::RESEAU_BOUCLE,
            3 => BouclageReseau::RESEAU_TRACE,
            default => null,
        };
    }

    public function volume_stockage(): float
    {
        if (2 !== $this->generateur_ecs->enum_type_stockage_ecs_id) {
            return 0;
        }
        return $this->generateur_ecs->volume_stockage > 0 ? $this->generateur_ecs->volume_stockage : 0;
    }

    public function position_volume_chauffe_stockage(): ?bool
    {
        if (0 === $this->volume_stockage()) {
            return null;
        }
        return $this->generateur_ecs->position_volume_chauffe_stockage
            ?? $this->generateur_ecs->position_volume_chauffe
            ?? false;
    }

    public function __invoke(GenerateurEcs $generateur_ecs, InstallationEcs $installation_ecs, Context $context): SystemeDto
    {
        $this->generateur_ecs = $generateur_ecs;
        $this->installation_ecs = $installation_ecs;

        return new SystemeDto(
            id: $generateur_ecs->id(),
            description: $generateur_ecs->description(),
            generateur_id: $generateur_ecs->id(),
            installation_id: $installation_ecs->id(),
            reseau: new ReseauDto(
                alimentation_contigue: $this->alimentation_contigues(),
                niveaux_desservis: $this->niveaux_desservis(),
                isolation: $this->isolation_reseau(),
                bouclage: $this->bouclage_reseau(),
            ),
            stockage: new StockageDto(
                volume: $this->volume_stockage(),
                position_volume_chauffe: $this->position_volume_chauffe_stockage(),
            )
        );
    }
}
