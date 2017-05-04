<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/*
25769c6c-d34d-4bfe-ba98-e0ee856f3e7a
hex2bin(str_replace('-', '', '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a'))
*/
trait Uuidable
{

    /**
     * Event listeners - so we can tenant new objects
     */
    public static function bootUuidable()
    {
        var_dump('bootUuidable');
        static::creating(function($obj) {

            if (empty($obj->uuid) || static::findByUuid($obj->uuid)) {

                // Make 100% sure it's unique
                do {
                    $uuid = Uuid::uuid4();
                } while ($obj->findByUuid($uuid->toString()));

                // Set the uuid prior to save
                $obj->attributes['uuid'] = $uuid->toString();
            }
        });
    }

    /**
     * Find a model by its uuid
     *
     * @param $uuid
     * @return mixed
     */
    public static function findByUuid($uuid, $columns = ['*'])
    {
        return static::where('uuid', $uuid)->first($columns);
    }

    /**
     * Find a model by its UUID or throw an exception.
     *
     * @param  mixed  $uuid
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByUUIDOrFail($uuid, $columns = ['*'])
    {
        $result = static::findByUuid($uuid, $columns);

        if (is_array($uuid)) {

            if (count($result) == count(array_unique($uuid))) {
                return $result;
            }

        } elseif (! is_null($result)) {

            return $result;

        }

        throw (new ModelNotFoundException)->setModel(get_called_class());
    }

}
