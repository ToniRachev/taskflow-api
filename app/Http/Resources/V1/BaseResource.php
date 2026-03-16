<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    protected bool $detailed = false;
    protected bool $created = false;

    public function setDetailed(bool $value): static
    {
        $this->detailed = $value;
        return $this;
    }

    public function setCreated(bool $value): static
    {
        $this->created = $value;
        return $this;
    }

    public static function detailed($resource): self
    {
        return (new static($resource))->setDetailed(true);
    }

    public static function created($resource): self
    {
        return (new static($resource))->setCreated(true);
    }
}
