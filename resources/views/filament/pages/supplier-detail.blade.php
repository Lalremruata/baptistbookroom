<x-filament-panels::page>
<div class="lg:flex">
    <div class="w-full px-3 lg:w-2/3 lg:flex-none">
        {{ $this->table }}
    </div>
    <div class="px-3 lg:w-1/3 lg:flex-none">
        <x-filament::section>
            <div class="flex-auto">
            <div class="flex flex-col">
                <h5 class="mb-4 leading-normal font-bold">{{$this->record->supplier_name}}</h5>
                <span class="mb-2 leading-tight text-sm">A/C No.: <span class="font-semibold text-slate-700 sm:ml-2">{{$this->record->account_number}}</span></span>
                <span class="mb-2 leading-tight text-sm">Ph. Number: <span class="font-semibold text-slate-700 sm:ml-2">{{$this->record->contact_number}}</span></span>
                <span class="leading-tight text-sm">VAT Number: <span class="font-semibold text-slate-700 sm:ml-2">FRB1235476</span></span>
            </div>
            </div>
        </x-filament::section>
    </div>
</div>
<div class="w-full px-6 py-6 mx-auto">
    <div class="flex flex-wrap -mx-3">
          <div class="w-full max-w-full px-3 mt-6 md:w-7/12 md:flex-none">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
              <div class="p-6 px-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                <h6 class="mb-0">Billing Information</h6>
              </div>
              <div class="flex-auto p-4 pt-6">
                <ul class="flex flex-col pl-0 mb-0 rounded-lg">
                  <li class="relative flex p-6 mb-2 border-0 rounded-t-inherit rounded-xl bg-gray-50">
                    <div class="flex flex-col">
                      <h6 class="mb-4 leading-normal text-sm">Oliver Liam</h6>
                      <span class="mb-2 leading-tight text-xs">Company Name: <span class="font-semibold text-slate-700 sm:ml-2">Viking Burrito</span></span>
                      <span class="mb-2 leading-tight text-xs">Email Address: <span class="font-semibold text-slate-700 sm:ml-2">oliver@burrito.com</span></span>
                      <span class="leading-tight text-xs">VAT Number: <span class="font-semibold text-slate-700 sm:ml-2">FRB1235476</span></span>
                    </div>
                    <div class="ml-auto text-right">
                      <a class="relative z-10 inline-block px-4 py-3 mb-0 font-bold text-center text-transparent uppercase align-middle transition-all border-0 rounded-lg shadow-none cursor-pointer leading-pro text-xs ease-soft-in bg-150 bg-gradient-to-tl from-red-600 to-rose-400 hover:scale-102 active:opacity-85 bg-x-25 bg-clip-text" href="javascript:;"><i class="mr-2 far fa-trash-alt bg-150 bg-gradient-to-tl from-red-600 to-rose-400 bg-x-25 bg-clip-text"></i>Delete</a>
                      <a class="inline-block px-4 py-3 mb-0 font-bold text-center uppercase align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-pro text-xs ease-soft-in bg-150 hover:scale-102 active:opacity-85 bg-x-25 text-slate-700" href="javascript:;"><i class="mr-2 fas fa-pencil-alt text-slate-700" aria-hidden="true"></i>Edit</a>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="w-full max-w-full px-3 mt-6 md:w-5/12 md:flex-none">
            <div class="relative flex flex-col h-full min-w-0 mb-6 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
              <div class="p-6 px-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                <div class="flex flex-wrap -mx-3">
                  <div class="max-w-full px-3 md:w-1/2 md:flex-none">
                    <h6 class="mb-0">Your Transactions</h6>
                  </div>
                  <div class="flex items-center justify-end max-w-full px-3 md:w-1/2 md:flex-none">
                    <i class="mr-2 far fa-calendar-alt"></i>
                    <small>23 - 30 March 2020</small>
                  </div>
                </div>
              </div>
              <div class="flex-auto p-4 pt-6">
                <h6 class="mb-4 font-bold leading-tight uppercase text-xs text-slate-500">Newest</h6>
                <ul class="flex flex-col pl-0 mb-0 rounded-lg">
                  <li class="relative flex justify-between px-4 py-2 pl-0 mb-2 bg-white border-0 rounded-t-inherit text-inherit rounded-xl">
                    <div class="flex items-center">
                      <button class="leading-pro ease-soft-in text-xs bg-150 w-6.35 h-6.35 p-1.2 rounded-3.5xl tracking-tight-soft bg-x-25 mr-4 mb-0 flex cursor-pointer items-center justify-center border border-solid border-red-600 border-transparent bg-transparent text-center align-middle font-bold uppercase text-red-600 transition-all hover:opacity-75"><i class="fas fa-arrow-down text-3xs"></i></button>
                      <div class="flex flex-col">
                        <h6 class="mb-1 leading-normal text-sm text-slate-700">Netflix</h6>
                        <span class="leading-tight text-xs">27 March 2020, at 12:30 PM</span>
                      </div>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                      <p class="relative z-10 inline-block m-0 font-semibold leading-normal text-transparent bg-gradient-to-tl from-red-600 to-rose-400 text-sm bg-clip-text">- $ 2,500</p>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
</div>
</div>
</x-filament-panels::page>
