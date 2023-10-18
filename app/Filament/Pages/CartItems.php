<?php

namespace App\Filament\Pages;

use App\Models\CartItem;
use App\Models\Item;
use Filament\Actions\Action;
// use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Card;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class CartItems extends Page implements HasForms, HasTable
{
    protected static ?string $model = CartItem::class;
    public CartItem $cartItem;
    use InteractsWithTable;
    use InteractsWithForms;
    public ?array $data = [];
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.cart-items';
    public function mount(): void
    {
        $this->form->fill();
        // $this->form->fill(auth()->user()->cartitem->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                ->schema([
                    Select::make('item_id')
                    ->options(Item::query()->pluck('item_name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                    Hidden::make('user_id')
                    ->default(auth()->user()->id)
                ])->columns(2)

            ])
            ->statePath('data');
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(CartItem::query())
            ->columns([
                TextColumn::make('item_id'),
                TextColumn::make('quantity'),
                TextColumn::make('user_id'),
            ])
            ->actions([
                DeleteAction::make()
            ])
            ->bulkActions([
                // ...
            ])
            ->headerActions([
                BulkAction::make('check out cartcd')
                ->requiresConfirmation()
                ->action(fn (Collection $records) => $records->each->delete())
            ]);
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Add to cart'))
                ->submit('save'),
        ];
    }
    public function save(): void
    {
        try {
            $data = $this->form->getState();
            // dd(auth()->user()->id);
            CartItem::create($data);
            // auth()->cartitem->save($data);
        } catch (Halt $exception) {
            return;
        }
    }
    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => $this->cartItem->delete());
    }
}
