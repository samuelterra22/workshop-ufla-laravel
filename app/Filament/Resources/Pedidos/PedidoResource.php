<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pedidos;

use App\Actions\Pedidos\CancelarPedido;
use App\Enums\CanalPedido;
use App\Enums\StatusPedido;
use App\Filament\Resources\Pedidos\Pages\ListPedidos;
use App\Models\Pedido;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Resource em arquivo único, para contraste com o ClienteResource.
 *
 * Repare na ação "Cancelar": ela NÃO implementa a regra. Ela delega para a
 * Action do domínio. O Resource é camada HTTP, igual a um controller.
 */
final class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $modelLabel = 'pedido';

    protected static ?string $pluralModelLabel = 'pedidos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('cliente_id')
                ->label('Cliente')
                ->relationship('cliente', 'nome')
                ->searchable()
                ->required(),

            Select::make('canal')
                ->label('Canal')
                ->options(CanalPedido::class)
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options(StatusPedido::class)
                ->required(),

            Textarea::make('observacao')
                ->label('Observação')
                ->maxLength(500)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('cliente.nome')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('canal')
                    ->label('Canal')
                    ->badge(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('total_em_centavos')
                    ->label('Total')
                    ->money('BRL', divideBy: 100)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(StatusPedido::class),
                SelectFilter::make('canal')->options(CanalPedido::class),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(static fn (Pedido $record): bool => $record->status->podeSerCancelado())
                    ->action(static fn (Pedido $record) => app(CancelarPedido::class)->handle($record)),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Eager loading no ponto de entrada da listagem: sem isso, cada linha
     * dispara uma consulta para buscar o cliente.
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('cliente');
    }

    /**
     * @return array<string, class-string>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListPedidos::route('/'),
        ];
    }
}
