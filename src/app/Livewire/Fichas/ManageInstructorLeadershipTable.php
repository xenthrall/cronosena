<?php

namespace App\Livewire\Fichas;

use App\Models\FichaInstructorLeadership;
use App\Models\Ficha;

use Livewire\Component;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Contracts\HasTable;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Tables\Concerns\InteractsWithTable;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

use Filament\Actions\ActionGroup;
use App\Filament\Actions\InstructorLeader\EditEndDate;
use Filament\Actions\DeleteAction;

class ManageInstructorLeadershipTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public Ficha $ficha;

    public function table(Table $table): Table
    {
        return $table
            
            ->query(FichaInstructorLeadership::query()->where('ficha_id', $this->ficha->id)->orderByDesc('start_date'))
            ->columns([
                ImageColumn::make('instructor.photo_url')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->toggleable(false),
                TextColumn::make('instructor.document_number')
                    ->label('Documento'),

                TextColumn::make('instructor.full_name')
                    ->label('Instructor'),

                TextColumn::make('start_date')
                    ->label('Fecha de Inicio')
                    ->date('d/F/Y'),
                TextColumn::make('end_date')
                    ->label('Fecha de Fin')
                    ->date('d/F/Y'),
                TextColumn::make('is_active')
                    ->label('estado')
                    ->formatStateUsing(fn($state) => $state ? 'Gestor Actual' : 'Gestor Anterior'),
            ])

            ->recordActions([
                ActionGroup::make([
                    EditEndDate::make(),
                    DeleteAction::make()
                        ->label('Eliminar asignación')
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar asignación de instructor líder')
                        ->modalDescription('¿Estás seguro de que deseas eliminar esta asignación? Esta acción no se puede deshacer.')
                        ->successNotificationTitle('Asignación eliminada con éxito.'),
                ]),
            ]);
    }
    public function render()
    {
        return view('livewire.fichas.manage-instructor-leadership-table');
    }
}
