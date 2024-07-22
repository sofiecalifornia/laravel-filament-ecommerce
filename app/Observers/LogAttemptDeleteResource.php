<?php

declare(strict_types=1);

namespace App\Observers;

use App\Exceptions\DeletingResourceException;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;

trait LogAttemptDeleteResource
{
    /**
     * @throws Halt
     */
    private static function abortThenLogAttemptDelete(
        Model $model,
        string $errorMessage,
        array $properties,
    ): void {

        foreach ($properties as $property) {
            if (is_array($property)) {
                throw new \Exception('Properties value must not array.');
            }
        }

        activity('admin')
            ->performedOn($model)
            ->event('deleting-attempt')
            ->withProperties($properties)
            ->log('Attempted to delete resource.');

        if (Filament::isServing()) {

            Notification::make()
                ->title(trans('Failed to delete resource.'))
                ->body($errorMessage)
                ->persistent()
                ->warning()
                ->send();

            throw (new Halt())->rollBackDatabaseTransaction();
        }

        throw DeletingResourceException::cannotDeleteParentResourceWhileHasChildResource($errorMessage);
    }

    /**
     * @param  non-empty-string  $relationshipName
     *
     * @throws Halt
     */
    private static function abortThenLogAttemptDeleteRelationCount(
        Model $model,
        string $errorMessage,
        string $relationshipName, // TODO: check relation ship string.
        int $relationshipCount,
    ): void {
        if ($relationshipCount < 1) {
            throw new \Exception('$relationshipCount should not below 1.');
        }

        self::abortThenLogAttemptDelete($model, $errorMessage, [
            'relationship_name' => $relationshipName,
            'relationship_count' => $relationshipCount,
        ]);

    }
}
