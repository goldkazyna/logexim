@extends('layouts.cabinet')
@section('title', 'Шаблоны описания')
@push('styles')
<style>.common_btn{background-color:#D0171C;border-color:#D0171C;border-radius:10px;color:#fff;padding:8px 20px;border:none;cursor:pointer;text-decoration:none;display:inline-block}.common_btn:hover{background-color:#a21216}</style>
@endpush
@section('content')
<main class="flex-grow p-6">
    <div class="flex items-center justify-between flex-wrap gap-2 mb-6">
        <div>
            <div class="card overflow-hidden">
                <div class="card-header"><h4 class="text-slate-900 text-lg font-medium mb-2">Шаблон описания</h4></div>
                <div><div class="overflow-x-auto"><div class="min-w-full inline-block align-middle"><div class="overflow-hidden" style="padding:20px">
                    <p><b>Шаблон описания</b> — это инструмент для ускорения процесса создания накладных.<br>Когда вы заполняете накладную, необходимо ввести описание отправления.<br>Чтобы не вводить эти данные каждый раз заново, воспользуйтесь шаблоном.</p>
                </div></div></div></div>
            </div>
            @if(session('success_message'))
            <div id="success-alert" class="bg-primary text-sm text-white rounded-md p-4 mb-4 mt-4"><span class="font-bold">Успешно!</span> {{ session('success_message') }}</div>
            @endif
            <div class="mt-4"><a href="{{ url('cabinet/add_description_templates') }}" class="common_btn">Добавить шаблон</a></div>
            <br>
            <div class="card overflow-hidden">
                <div class="card-header"><h4 class="card-title">Шаблоны описания</h4></div>
                <div><div class="overflow-x-auto"><div class="min-w-full inline-block align-middle"><div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead><tr>
                            <th class="px-6 py-3 text-start text-sm text-gray-500">Название</th>
                            <th class="px-6 py-3 text-start text-sm text-gray-500">Описание вложения</th>
                            <th class="px-6 py-3 text-start text-sm text-gray-500">Кол-во мест</th>
                            <th class="px-6 py-3 text-start text-sm text-gray-500">Вес (кг)</th>
                            <th class="px-6 py-3 text-start text-sm text-gray-500">Объёмный вес (кг)</th>
                            <th class="px-6 py-3 text-end text-sm text-gray-500">Действие</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($templates as $template)
                            <tr id="template-{{ $template->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $template->template_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $template->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $template->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $template->weight }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $template->volume_weight }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                    <a href="/cabinet/edit_description_templates/{{ $template->id }}" class="text-primary hover:text-sky-700">Редактировать</a> /
                                    <a class="text-primary hover:text-sky-700 cursor-pointer delete-template" data-id="{{ $template->id }}">Удалить</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-6 py-4 text-sm text-gray-800">Шаблоны описания отсутствуют.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div></div></div></div>
            </div>
        </div>
    </div>
</main>
@endsection
@push('scripts')
<script>
$(document).ready(function(){
    setTimeout(function(){$('#success-alert').fadeOut('slow')},5000);
    $('.delete-template').click(function(){
        var id=$(this).data('id');
        $.ajax({url:'/cabinet/delete_description_template',type:'POST',data:{id:id},success:function(res){
            if(res.status==='success'){$('#template-'+id).fadeOut('slow',function(){$(this).remove()});}
            else{alert('Ошибка при удалении');}
        }});
    });
});
</script>
@endpush
