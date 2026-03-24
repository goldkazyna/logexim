@extends('layouts.admin')
@section('title', isset($order) ? 'Редактировать заказ' : 'Добавить заказ')
@section('content')
<div class="card">
    <div class="card-header">{{ isset($order) ? 'Редактировать заказ' : 'Новый заказ' }}</div>
    <div class="card-body">
        <form action="{{ isset($order) ? url('admin/orders/update/'.$order->id) : url('admin/orders/store') }}" method="post">
            @csrf
            <div class="form-group"><label>Трек-номер</label><input type="text" name="track_number" class="form-control" value="{{ $order->track_number ?? '' }}" required></div>
            <div class="form-group"><label>Отправитель</label><input type="text" name="name_from" class="form-control" value="{{ $order->name_from ?? '' }}" required></div>
            <div class="form-group"><label>Получатель</label><input type="text" name="name_to" class="form-control" value="{{ $order->name_to ?? '' }}" required></div>
            <div class="form-group"><label>Дата отправки</label><input type="date" name="date_from" class="form-control" value="{{ $order->date_from ?? '' }}" required></div>
            <div class="form-group"><label>Дата доставки</label><input type="date" name="date_to" class="form-control" value="{{ $order->date_to ?? '' }}" required></div>
            <div class="form-group"><label>Статус</label>
                <select name="status" class="form-control">
                    <option value="0" {{ (isset($order) && $order->status==0)?'selected':'' }}>В пути</option>
                    <option value="1" {{ (isset($order) && $order->status==1)?'selected':'' }}>Доставлен</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</div>
@endsection
