<x-filament::page>
    <form wire:submit.prevent="submit">
        <div class="grid grid-cols-1 gap-6 mb-6">
            <span class="text-sm font-medium leading-4 text-gray-700">
                Customers
            </span>
            <div class="space-y-6 rounded-xl">
                <ul>
                    <div class="grid grid-cols-1 gap-6">
                        @foreach ($customers as $index => $item)
                            <li class="bg-white border border-gray-300 shadow-sm rounded-xl relative">
                                <header class="flex flex-row-reverse items-center h-10 overflow-hidden border-b bg-gray-50 rounded-t-xl">
                                    <ul class="flex divide-x rtl:divide-x-reverse">
                                        <li>
                                            <button type="button" wire:click="removerCustomer({{$index}})"
                                                class="flex items-center justify-center flex-none w-10 h-10 text-danger-600 transition hover:text-danger-500">
                                                <span class="sr-only">
                                                    Excluir
                                                </span>
                                                <svg class="w-4 h-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                    aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd">
                                                    </path>
                                                </svg>
                                            </button>
                                        </li>
                                    </ul>
                                </header>
                                <div class="p-6">
                                    <div class="grid grid-cols-1 gap-6">
                                        <div>
                                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                                                <div class="col-span-1">
                                                    <div class="space-y-2">
                                                        <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                            <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse">
                                                                <span class="text-sm font-medium leading-4 text-gray-700">
                                                                    Código do customer
                                                                    <sup class="font-medium text-danger-700">*</sup>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="flex items-center space-x-2 rtl:space-x-reverse group">
                                                            <input wire:change="setarValores({{$index}})" wire:model="customers.{{$index}}.customer_id" id="customer_id_{{$index}}" type="number" autofocus required class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-span-3">
                                                    <div class="space-y-2">
                                                        <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                            <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse">
                                                                <span class="text-sm font-medium leading-4 text-gray-700">
                                                                    Nome do customer
                                                                    <sup class="font-medium text-danger-700">*</sup>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="flex items-center space-x-2 rtl:space-x-reverse group">
                                                            <input wire:model="customers.{{$index}}.nome"  type="text" required disabled class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                                <div>
                                                    <div class="space-y-2">
                                                        <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                            <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse">
                                                                <span class="text-sm font-medium leading-4 text-gray-700">
                                                                    Dívida
                                                                    <sup class="font-medium text-danger-700">*</sup>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="flex items-center space-x-2 rtl:space-x-reverse group">
                                                            <input wire:model="customers.{{$index}}.divida"  type="number" required disabled class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="space-y-2">
                                                        <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                            <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse">
                                                                <span class="text-sm font-medium leading-4 text-gray-700">
                                                                    Valor pago
                                                                    <sup class="font-medium text-danger-700">*</sup>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="flex items-center space-x-2 rtl:space-x-reverse group">
                                                            <input wire:model="customers.{{$index}}.pago" type="number" min="0" max="{{$customers[$index]['divida']}}" required class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="space-y-2">
                                                        <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                            <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse">
                                                                <span class="text-sm font-medium leading-4 text-gray-700">
                                                                    Valor comprado
                                                                    <sup class="font-medium text-danger-700">*</sup>
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="flex items-center space-x-2 rtl:space-x-reverse group">
                                                            <input wire:model="customers.{{$index}}.comprado" type="number" min="0" required class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </div>
                </ul>
            </div>
        </div>
        <div class="flex justify-center">
            <button type="button" wire:click="adicionarCustomer"
                class="inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
                Adicionar em customers
            </button>
        </div>
        <button type="submit"
            class="inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
            Enviar
        </button>
        <button type="button"
            wire:click="submitAndPrint"
            class="inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600">
            Enviar e imprimir
        </button>
    </form>
    <script>
        document.addEventListener('keydown', function (event) {
            if (event.keyCode === 13 && event.target.nodeName === 'INPUT') {
                var form = event.target.form;
                var index = Array.prototype.indexOf.call(form, event.target);
                count = 1;
                while (count + index <= form.elements.length && form.elements[index + count].disabled) {
                    count += 1;
                }
                form.elements[index + count].focus();
                event.preventDefault();
            }
        });
        window.addEventListener('focus_next_input', event => {
            document.getElementById('customer_id_' + event.detail.index).focus();
            window.scrollTo(0, document.body.scrollHeight);
        })
        window.addEventListener('select_text_in_input_with_focus', event => {
            document.activeElement.select();
        })
        window.onbeforeunload = function(e) {
            return "Do you want to exit this page?";
        };
    </script>
</x-filament::page>
