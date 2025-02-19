<?php

namespace Silber\Bouncer\Contracts;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\Eloquent\Model;

interface CachedClipboard extends Clipboard
{
    /**
     * Set the cache instance.
     *
     * @return $this
     */
    public function setCache(Store $cache);

    /**
     * Get the cache instance.
     *
     * @return \Illuminate\Contracts\Cache\Store
     */
    public function getCache();

    /**
     * Get a fresh copy of the given authority's abilities.
     *
     * @param  bool  $allowed
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFreshAbilities(Model $authority, $allowed);

    /**
     * Clear the cache.
     *
     * @param  null|\Illuminate\Database\Eloquent\Model  $authority
     * @return $this
     */
    public function refresh($authority = null);

    /**
     * Clear the cache for the given authority.
     *
     * @return $this
     */
    public function refreshFor(Model $authority);
}
