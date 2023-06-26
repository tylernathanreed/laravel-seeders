<?php

namespace Reedware\LaravelSeeders;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ResourceRepository
{
    /**
     * The cache repository implementation.
     */
    protected Repository $cache;

    /**
     * The cache key prefix.
     */
    protected string $prefix;

    /**
     * Creates a new resource repository instance.
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;

        // If multiple application instances are being run in parallel,
        // and they are all talking to the same cache, we could run
        // into trouble here. We'll prefix internally to fix it.

        $this->prefix = Uuid::uuid4()->toString() . '.';
    }

    /**
     * Adds the given resource to this repository.
     */
    public function add(string $resource, Model $instance): void
    {
        $count = $this->increment($resource) - 1;

        $this->cache->forever($this->prefix . $resource . '.' . $count, $instance->getKey());
    }

    /**
     * Pulls all of the resources for the given type.
     */
    public function pull(string $resource): array
    {
        $count = $this->count($resource);

        for ($i = 0; $i < $count; $i++) {
            $keys[] = $this->cache->pull($this->prefix . $resource . '.' . $i);
        }

        $this->cache->forget($this->prefix . $resource);

        return $keys ?? [];
    }

    /**
     * Returns the number of resources within this repository.
     */
    public function count(string $resource): int
    {
        return $this->cache->get($this->prefix . $resource, 0);
    }

    /**
     * Increases the count for the given resource.
     */
    public function increment(string $resource): int
    {
        return $this->cache->increment($this->prefix . $resource, 1);
    }
}
