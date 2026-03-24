<table class="min-w-full divide-y divide-gray-200">
    <thead>
        <tr>
            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Дата</th>
            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Номер накладной</th>
            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Отправитель</th>
            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Получатель</th>
            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Количество мест</th>
            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Вес (кг)</th>
            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Объявленная ценность</th>
            <th scope="col" class="px-6 py-3 text-start text-sm text-gray-500">Статус</th>
            <th scope="col" class="px-6 py-3 text-end text-sm text-gray-500">Действие</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        @forelse($invoices as $invoice)
        <tr id="invoice-{{ $invoice->id }}">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ \Carbon\Carbon::parse($invoice->date)->format('d.m.Y') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ $invoice->invoice_number }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ $invoice->sender_company }}<br>
                {{ $invoice->sender_name }}<br>
                {{ $invoice->sender_phone }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ $invoice->recipient_company }}<br>
                {{ $invoice->recipient_name }}<br>
                {{ $invoice->recipient_phone }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ $invoice->quantity }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ $invoice->weight }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                {{ $invoice->declared_value }} KZT
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                @switch($invoice->status)
                    @case(0)<span style="background-color:#00056d; color:#ffffff; padding:10px; border-radius:10px;">Заявка создана</span>@break
                    @case(1)<span style="background-color:#ffcc00; color:#ffffff; padding:10px; border-radius:10px;">Принята в работу</span>@break
                    @case(2)<span style="background-color:#00aaff; color:#ffffff; padding:10px; border-radius:10px;">Отправлено</span>@break
                    @case(3)<span style="background-color:#28a745; color:#ffffff; padding:10px; border-radius:10px;">Исполнена</span>@break
                    @case(4)<span style="background-color:red; color:#ffffff; padding:10px; border-radius:10px;">Отменена</span>@break
                    @default Неизвестный статус
                @endswitch
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium" align="center">
                <a href="{{ url('cabinet/view_invoice/' . $invoice->id) }}" class="icon-btn"><i class="fas fa-eye"></i></a>
                <a href="{{ url('cabinet/print_invoice/' . $invoice->id) }}" class="icon-btn ml-4" target="_blank"><i class="fas fa-print"></i></a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">Накладные отсутствуют.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="px-6 py-4">
    {{ $invoices->links() }}
</div>
