<?php

namespace App\Legacy\Transformer\Ecs;

use App\Domain\Ecs\Installation\Solaire\Usage;
use App\Dto\Ecs\Installation\{InstallationDto, SolaireThermiqueDto};
use App\Legacy\Model\InstallationEcs;
use App\Legacy\Transformer\Context;

final class InstallationTransformer
{
    private Context $context;
    private InstallationEcs $installation_ecs;

    public function surface(): float
    {
        return $this->installation_ecs->surface_habitable;
    }

    public function usage_solaire(): ?Usage
    {
        return match ($this->installation_ecs->enum_type_installation_solaire_id) {
            2, 3 => Usage::ECS,
            4 => Usage::CHAUFFAGE_ECS,
            default => null,
        };
    }

    public function annee_installation_solaire(): ?int
    {
        return match ($this->installation_ecs->enum_type_installation_solaire_id) {
            3 => $this->context->ressource()->administratif()->annee_etablissement(),
            default => null,
        };
    }

    public function installation_collective(): bool
    {
        return \in_array($this->installation_ecs->enum_type_installation_id, [2, 3, 4]);
    }

    public function fecs_saisi(): ?float
    {
        return match ($this->installation_ecs->enum_methode_saisie_fact_couv_sol_id) {
            2 => $this->installation_ecs->fecs > 0 ? $this->installation_ecs->fecs : null,
            default => null,
        };
    }

    public function __invoke(InstallationEcs $installation_ecs, Context $context): InstallationDto
    {
        $this->context = $context;
        $this->installation_ecs = $installation_ecs;

        return new InstallationDto(
            id: $installation_ecs->id(),
            description: $installation_ecs->description(),
            surface: $this->surface(),
            solaire_thermique: $this->usage_solaire() ? new SolaireThermiqueDto(
                usage: $this->usage_solaire(),
                annee_installation: $this->annee_installation_solaire(),
                fecs: $this->fecs_saisi(),
            ) : null,
        );
    }
}
