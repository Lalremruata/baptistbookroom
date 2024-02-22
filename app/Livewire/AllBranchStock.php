<?php

namespace App\Livewire;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class AllBranchStock extends Component implements HasForms, HasTable
{
    
    use InteractsWithTable;
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?string $navigationLabel = 'All Branch Stock Report';
    protected static ?int $navigationSort = 5;
    public function mount()
    {
        $this->branch = new Branch();
    }
    public function table(Table $table): Table
    {
        return $table
        ->relationship(fn (): HasMany => $this->branch->sales())
        // ->inverseRelationship('branch')
        ->columns([
            TextColumn::make('branch_name')
            ->searchable(),
        ]);
    }
    public function render()
    {
        return view('livewire.all-branch-stock');
    }
}
