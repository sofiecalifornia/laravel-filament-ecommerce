<?php

declare(strict_types=1);

namespace App\Providers\Macros;

use Closure;
use Filament\Actions\Contracts\HasRecord;
use Filament\Actions\MountableAction;
use Filament\Facades\Filament;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\ActivitylogServiceProvider;

/**
 * @mixin MountableAction
 */
class FilamentMountableActionMixin
{
    public function withActivityLog(): Closure
    {
        return fn (
            ?string $logName = null,
            Closure|string|null $event = null,
            Closure|string|null $description = null,
            Closure|array|null $properties = null,
            Model|int|string|null $causedBy = null
        ): MountableAction => $this->after(function (MountableAction $action) use ($logName, $event, $description, $properties, $causedBy) {

            $event = $action->evaluate($event) ?? $action->getName();
            $properties = $action->evaluate($properties);
            $description = Str::headline(
                $action->evaluate($description ?? $event) ?? $action->getName()
            );
            $causedBy ??= Filament::auth()->user();

            $log = function (?Model $model) use ($properties, $event, $logName, $description, $causedBy): void {
                if (null !== $model && $model::class === ActivitylogServiceProvider::determineActivityModel()) {
                    return;
                }

                activity($logName)
                    ->event($event)
                    ->causedBy($causedBy)
                    ->when(
                        $model,
                        fn (ActivityLogger $activityLogger, Model $model) => $activityLogger
                            ->performedOn($model)
                    )
                    ->withProperties($properties)
                    ->log($description);
            };

            if ($action instanceof BulkAction) {

                if ($action instanceof ExportBulkAction) {
                    $MODEL = $action->getExporter()::getModel();
                    $action->getRecords()
                        ?->each(fn (int|string $modelKey) => $log($MODEL::find($modelKey)));
                } else {
                    $action->getRecords()
                        ?->each(fn (Model $model) => $log($model));
                }

                return;
            }

            if ($action instanceof HasRecord) {
                $log($action->getRecord());
            }
        });
    }
}
