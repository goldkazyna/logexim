@extends('layouts.app')

@section('title', $page->title . ' - Logexim Express')

@push('styles')
<style>
.order-form .search-status { display: none; }
.order-form .search-status.active { display: block; }
.header { background-color: #000000 !important; position: inherit !important; }
.container { width:100%; }
.header-bottom { padding: 20px 0 33px; }
.content { margin-top:20px; }
@media (max-width:767px) { .header-bottom { padding: 0px 0 0px; } }
</style>
@endpush

@section('content')
<div class="container content">
    <div class="row">
        <div class="col-xs-12 col-lg-12">
            <div class="breadcrumbs">
                <span><a href="/">Главная</a></span> - <span>{{ $page->header }}</span>
            </div>
            <br />
            <section class="search">
                <div class="container">
                    <div class="search__wrapper">
                        <form class="search__form form order-form">
                            <h2>Найти посылку</h2>
                            <div class="form__row">
                                <label class="form-text form_col-6">
                                    <span class="form-text__desc form-text__desc_top">Введите трек номер:</span>
                                    <input type="text" name="search_track" data-mask="track" placeholder="______" required>
                                </label>
                                <button type="submit" class="btn form__submit form_col-4">Продолжить</button>
                            </div>
                            <div class="search-result">
                                <span class="_error" style="display:none;">По вашему трек номеру нет информации</span>
                                <div class="search-status" data-current="50">
                                    <div class="search-status__line">
                                        <div class="search-status__active"></div>
                                        <div class="search-status__start">
                                            <span class="search-status__label"><i class="icon-warehouse-fill"></i></span>
                                            <span class="search-status__desc">Отправлено: <span></span></span>
                                            <span class="search-status__desc">Отправитель: <span></span></span>
                                        </div>
                                        <div class="search-status__current">
                                            <span class="search-status__desc">В пути</span>
                                            <span class="search-status__label"><i class="icon-truck-fill"></i></span>
                                        </div>
                                        <div class="search-status__end">
                                            <span class="search-status__label"><i class="icon-finish_fill"></i></span>
                                            <span class="search-status__desc date_to">Ожидает доставки <span></span></span>
                                            <span class="search-status__desc name_to">Получатель: <span></span></span>
                                            <span class="search-status__desc success_delivery" style="display:none;color: green; font-weight: bold;">Доставлено</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.addEventListener('load', function(){
    document.querySelector('form.order-form').addEventListener('submit', function(e){
        e.preventDefault();
        this.querySelector('.search-result ._error').style = 'display:none;';
        this.querySelector('.search-result .search-status').classList.remove('active');
        let search_track = this.querySelector('input[name="search_track"]').value.trim();
        if(search_track.length < 6) return false;
        $.ajax({
            url: '/ajax/searchTrack', method: 'post',
            data: {search_track},
            success: (res) => {
                if(typeof res === 'string') res = JSON.parse(res.trim());
                let data = res;
                if(data.id && new Date().getTime() > new Date(data.date_from).getTime()){
                    let block_status = this.querySelector('.search-result .search-status');
                    block_status.querySelector('.search-status__start .search-status__desc span').innerText = new Date(data.date_from).toLocaleDateString();
                    block_status.querySelector('.search-status__start .search-status__desc:last-child span').innerText = data.name_from;
                    block_status.querySelector('.search-status__end .date_to span').innerText = new Date(data.date_to).toLocaleDateString();
                    block_status.querySelector('.search-status__end .name_to span').innerText = data.name_to;
                    block_status.querySelector('.success_delivery').style.display = 'none';
                    if(parseInt(data.status) === 1){
                        showStatusDelivery(100);
                        block_status.querySelector('.success_delivery').style.display = 'inline-block';
                    }else{
                        let totalTime = new Date(data.date_to).getTime() - new Date(data.date_from).getTime();
                        let currTime = new Date().getTime() - new Date(data.date_from).getTime();
                        totalTime = totalTime / 1000 / 3600;
                        currTime = currTime / 1000 / 3600;
                        let percent = currTime / totalTime * 100;
                        showStatusDelivery(percent);
                    }
                    block_status.classList.add('active');
                }else{
                    this.querySelector('.search-result ._error').style = '';
                }
            }
        })
    })
    document.querySelector('input[data-mask="track"]').addEventListener('input', function(){
        let value = this.value.trim().replace(/[^0-9]{1,}/g, '')
        value = value.slice(0, 6);
        this.value = value;
    })
})
</script>
@endpush
