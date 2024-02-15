<?php

namespace App\Filament\Pages;

use App\Models\Item;
use App\Models\SubCategory;
use Filament\Pages\Page;


class AllBranchStocks extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.all-branch-stocks';

}
