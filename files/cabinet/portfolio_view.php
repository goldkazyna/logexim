<?php if($user['status'] != '1' && $user['status'] != '2') { header("Location: /cabinet"); } ?>
<?$this->load->view("templates/cabinet/left_menu_view")?>
<style>
.fill-order form .tit {
    margin: 0 0 17px 0;
}
.photos .ph-blocks {
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flex;
    display: -o-flex;
    display: flex;
    flex-wrap: wrap;
    margin: 0 -8px;
}
.photos .photo {
    margin: 0 0 15px 0;
    padding: 0 8px;
}
.photos .photo input {
    display: none;
}
.photos .photo label {
    width: 105px;
    height: 105px;
    border-radius: 2px;
    border: 1px solid #c9c9c9;
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flex;
    display: -o-flex;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 27px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    color: #919191;
}
.column{
    width:25%;
}
</style>

                    <div class="col-12 col-md-12 col-lg order-2 order-md-3 order-lg-2">
                        <div class="tech-middle">
                            <div class="for-title">
                                <div class="title fs-38 fs-mob-25 fs-lg-30 mb-3">
                                    Портфолио | <?echo $user['name']?>
                                </div>
                                <p class="fs-16">Разместите портфолио Ваших работ, что бы посетители сайта могли увидеть</p>
                            </div>
                            <div class="tm-blocks">
                                
                                    <div class="tm-block">
                                        <div class="photos">

                               
<form id='postform' action="/cabinet/add_portfolio" method="post" enctype="multipart/form-data">
                                <div class="ph-blocks">
                                    <div class="photo">
                                        <input name="photo[]" id="photo1" type="file">
                                        <label for="photo1">+</label>
                                    </div>
                                    <div class="photo">
                                        <input name="photo[]" id="photo2" type="file">
                                        <label for="photo2">+</label>
                                    </div>
                                    <div class="photo">
                                        <input name="photo[]" id="photo3" type="file">
                                        <label for="photo3">+</label>
                                    </div>
                                    <div class="photo">
                                        <input name="photo[]" id="photo4" type="file">
                                        <label for="photo4">+</label>
                                    </div>

                                </div>
                               
                                        <div class="ph-blocks">
                                            <div class="photo">
                                                <input name="photo[]" id="photo5" type="file">
                                                <label for="photo5">+</label>
                                            </div>
                                            <div class="photo">
                                                <input name="photo[]" id="photo6" type="file">
                                                <label for="photo6">+</label>
                                            </div>
                                            <div class="photo">
                                                <input name="photo[]" id="photo7" type="file">
                                                <label for="photo7">+</label>
                                            </div>
                                            <div class="photo">
                                                <input name="photo[]" id="photo8" type="file">
                                                <label for="photo8">+</label>
                                            </div>
                                        </div>
                                </form>
                                <div class="order my-btn more-photo-btn">
                                    <a class="cl-btn btn collapsed" href="#" data-toggle="collapse" onClick='formvlidate()'>
                                        <p class="tt-1">Добавить фото в портфолио</p>
                                        
                                    </a>
                                </div>
                            </div>
                                    </div>
                                    <div class="tm-block">
                                        
                                       Ваше фото
                                        <hr />
                                	<div class = "row">
                                    	<?php foreach ($user_portfolio as $item):?>
                                        <a class="column" id="zoomIn" style="position: relative;"><span class="delete-item" data-id="<?php echo $item['id']; ?>" style="position: absolute;width:16px;right: 5px;top:0;cursor: pointer;z-index: 9999;"><svg viewBox="0 0 136.93 136.93" xmlns="http://www.w3.org/2000/svg">
                                                 <g fill="#ed6868">
                                                  <path d="m132.99 99.316c-6.597-6.595-13.189-13.188-19.785-19.783-3.288-3.288-6.576-6.577-9.864-9.865-1.915-1.915-0.106-2.309 1.725-4.14 8.093-8.095 16.188-16.19 24.281-24.283 2.697-2.7 5.927-5.249 6.376-9.333 0.617-5.644-4.11-9.153-7.651-12.693-5.773-5.773-17.326-22.657-27.023-16.643-7.658 4.75-14.12 13.764-20.432 20.076-3.479 3.479-6.957 6.957-10.436 10.434-2.15 2.15-1.827 1.591-3.996-0.576-8.051-8.051-16.104-16.102-24.157-24.153-2.328-2.328-4.845-5.592-8.057-6.692-6.127-2.095-10.011 2.47-13.826 6.281-5.602 5.602-21.684 16.598-18.467 26.012 1.098 3.213 4.361 5.729 6.688 8.059l13.212 13.212c3.648 3.648 7.296 7.296 10.945 10.947 2.167 2.167 2.726 1.845 0.576 3.996-7.969 7.967-15.936 15.936-23.905 23.904-2.152 2.152-4.979 4.335-6.608 6.962-3.749 6.042 0.466 10.843 4.523 14.898 5.48 5.48 15.905 20.748 24.816 19.772 8.071-0.88399 17.021-14.063 22.218-19.261 4.016-4.012 8.029-8.025 12.043-12.039 2.167-2.167 1.845-2.726 3.994-0.575 8.245 8.242 16.489 16.485 24.732 24.729 2.883 2.883 5.76 6.67 10.104 7.146 5.642 0.617 9.15-4.11 12.691-7.65 5.942-5.942 24.881-19.152 15.283-28.742"/>
                                                  <path d="m32.108 5.382c2.126 2.13-22.226 24.48-25.087 26.422-4.748 3.223 4.856-9.947 5.78-10.959 1.488-1.628 16.647-18.123 19.307-15.463"/>
                                                  <path d="m38.985 73.616c2.172 2.18-28.186 30.286-31.196 32.637-1.428 1.116-3.703 2.102-2.241-0.648 2.541-4.777 6.725-8.94 10.396-12.841 1.652-1.754 20.405-21.785 23.041-19.148"/>
                                                  <path d="m99.911 73.616c-2.167 2.172 28.184 30.286 31.193 32.637 1.429 1.116 3.705 2.102 2.243-0.648-2.539-4.777-6.724-8.94-10.394-12.841-1.65-1.754-20.405-21.785-23.042-19.148"/>
                                                  <path d="m32.108 5.382c1.841-1.838 36.188 33.275 34.731 34.733-1.558 1.562-39.278-30.17-34.731-34.733"/>
                                                  <path d="m105.08 5.382c-2.129 2.132 22.224 24.48 25.086 26.422 4.746 3.223-4.855-9.947-5.781-10.959-1.485-1.626-16.649-18.123-19.305-15.463"/>
                                                  <path d="m105.08 5.382c-1.836-1.834-35.94 33.008-34.476 34.473 1.579 1.584 39.015-29.926 34.476-34.473"/>
                                                  <path d="m132.57 31.309c-1.838-1.838-20.787 18.733-22.4 20.36-3.009 3.038-7.01 6.294-9.028 10.148-1.559 2.978 5.516-1.839 5.892-2.13 3.065-2.374 29.68-24.242 25.536-28.378"/>
                                                  <path d="m68.087 95.791c-1.589-1.594-19.854 17.809-21.491 19.445-4.685 4.684-9.672 9.238-13.859 14.391-2.32 2.854-1.628 4.019 1.6 1.941 3.205-2.06 37.682-31.851 33.75-35.777"/>
                                                  <path d="m6.914 31.309c1.829-1.833 20.79 18.735 22.399 20.36 3.011 3.04 7.008 6.294 9.029 10.148 1.56 2.978-5.514-1.839-5.89-2.13-3.064-2.371-29.683-24.255-25.538-28.378"/>
                                                  <path d="m71.536 95.937c1.603-1.607 19.133 17.082 20.689 18.639 4.686 4.684 9.672 9.24 13.857 14.39 2.317 2.855 1.627 4.018-1.599 1.942-3.206-2.061-36.933-30.998-32.947-34.971"/>
                                                  <path d="m30.881 0.022c-7.374 0-14.027 9.615-18.709 14.297-5.675 5.674-16.408 13.272-10.383 22.412 3.84 5.826 10.373 10.68 15.26 15.567 5.387 5.388 10.777 10.771 16.161 16.163-8.732 8.721-17.454 17.452-26.181 26.179-4.257 4.257-8.981 8.757-6.191 15.486 2.154 5.201 8.574 9.712 12.414 13.552 3.698 3.698 7.337 8.499 11.725 11.435 6.522 4.365 12.054 0.047989 16.563-4.459 8.978-8.978 17.962-17.951 26.934-26.934 8.722 8.732 17.454 17.454 26.181 26.179 4.257 4.255 8.753 8.979 15.485 6.191 5.199-2.153 9.711-8.571 13.552-12.412 3.697-3.697 8.499-7.335 11.434-11.723 4.325-6.462 0.057999-12.045-4.458-16.563-8.979-8.978-17.952-17.96-26.935-26.932 8.722-8.732 17.454-17.456 26.182-26.183 4.242-4.242 8.927-8.711 6.212-15.419-2.119-5.23-8.59-9.774-12.435-13.62-3.701-3.699-7.355-8.548-11.768-11.46-6.517-4.301-12.021-0.013-16.521 4.485-8.978 8.978-17.96 17.951-26.932 26.934-6.127-6.135-12.26-12.26-18.39-18.392-4.735-4.732-11.717-14.787-19.2-14.783m-5.93 132.15c-6.719-6.717-29.727-22.531-19.429-32.829 10.296-10.294 20.59-20.591 30.887-30.886-6.553-6.552-13.106-13.107-19.661-19.66-5.181-5.181-20.182-15.68-11.922-23.938 6.609-6.608 13.221-13.217 19.831-19.825 8.307-8.303 17.728 5.276 23.09 10.636 6.891 6.891 13.782 13.782 20.673 20.671 0.104 0.104 17.34-17.238 18.626-18.522 5.156-5.156 16.596-21.437 24.944-13.079 7.122 5.452 13.205 13.201 19.557 19.548 8.67 8.668-4.56 17.713-10.288 23.443-6.909 6.909-13.817 13.818-20.727 20.728 10.312 10.309 22.737 20.149 31.657 31.657 8.243 8.24-7.113 18.976-12.248 24.109-5.53 5.527-12.562 15.199-20.584 7.178-6.651-6.65-13.301-13.302-19.953-19.952-2.148-2.147-9.521-12.34-12.118-9.746-6.862 6.862-13.724 13.722-20.585 20.584-4.958 4.954-13.975 17.677-21.75 9.883"/>
                                                 </g>
                                                </svg>
                                            </span><figure><img src = "/images/post/thumbs/<?echo $item['thumb_229_162']?>"></figure></a>
                                        <? endforeach;?>
                                    		
                                    </div>
                                    </div>
                                
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
<style>
.column {
	display: inline-block;
	text-align: center;
}

figure {
	overflow: hidden;
}

a p {
	color: black;
	margin-top: 8px;
	font-family: 'Open Sans', sans-serif;
}

a:hover {
	text-decoration: none;
}

.column img {
	display: block;
	width: 100%;
}

/* CSS Image Hover. Created refering to Naoya's Pen: https://codepen.io/nxworld/pen/ZYNOBZ */
/*Sepia*/
.column#sepia img {
	-webkit-filter: sepia(100%);
	filter: sepia(100%);
	-webkit-transition: .3s ease-in-out;
	transition: .3s ease-in-out;
}
.column#sepia:hover img {
	-webkit-filter: sepia(0);
	filter: sepia(0);
}

/*GrayScale*/
.column#grayscale img {
	-webkit-filter: grayscale(100%);
	filter: grayscale(100%);
	-webkit-transition: .3s ease-in-out;
	transition: .3s ease-in-out;
}
.column#grayscale:hover img {
	-webkit-filter: grayscale(0);
	filter: grayscale(0);
}

/*Zoom In*/
.column#zoomIn img {
	-webkit-transform: scale(1);
	transform: scale(1);
	-webkit-transition: .3s ease-in-out;
	transition: .3s ease-in-out;
}
.column#zoomIn:hover img {
	-webkit-transform: scale(1.3);
	transform: scale(1.3);
}

/*Zoom Out*/
.column#zoomOut img {
	-webkit-transform: scale(1.5);
	transform: scale(1.5);
	-webkit-transition: .3s ease-in-out;
	transition: .3s ease-in-out;
}
.column#zoomOut:hover img {
	-webkit-transform: scale(1);
	transform: scale(1);
}

/* 3D Transform. Craeted refering to Dudley Storey's Pen: https://codepen.io/dudleystorey/pen/KFLJp */
.tdimension {
	width: 300px;
	height: 300px;
	margin: 20px auto 40px auto;
	perspective: 1000px;
}
.tdimension a {
	display: block;
	width: 100%;
	height: 100%;
	background: url("https://mir-s3-cdn-cf.behance.net/project_modules/disp/e8346826957515.56361c2106f3f.png");
	background-size: cover;
	transform-style: preserve-3d;
	transform: rotateX(70deg);
	transition: all 0.8s;	
}
.tdimension:hover a {
	transform: rotateX(20deg); 	
}	
.tdimension a:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 40px;
    background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.1));    
   	transform: rotateX(90deg);
    transform-origin: bottom;
}

/*With Simple Caption*/
.column#caption {
	position: relative;
}
.column#caption .text {
		position: absolute;		
    top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		z-index: 10;
    opacity: 0;
    transition: all 0.8s ease;			
}
.column#caption .text h1 {		
		margin: 0;		
		color: white;
}
.column#caption:hover .text {
	opacity: 1;
	
}
.column#caption:hover img {
	-webkit-filter: sepia(90%);
}

/* Craeted refering to LittleSnippets.net Pen: https://codepen.io/littlesnippets/pen/adLELd */
.frame {
	text-align: center;	
	position: relative;
	cursor: pointer;	
	perspective: 500px; 
}
.frame img {
	width: 300px;
	height: 300px;
}
.frame .details {
	width: 70%;
	height: 80%;	
	padding: 5% 8%;
	position: absolute;
	content: "";
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%) rotateY(90deg);
	transform-origin: 50%;
	background: rgba(255,255,255,0.9);	
	opacity: 0;
	transition: all 0.4s ease-in;
	
}
.frame:hover .details {
	transform: translate(-50%, -50%) rotateY(0deg);
	opacity: 1;
}

</style>
<script>
function formvlidate() {
        var err = false;
        if (err) {
            $('.error').text('Заполните выделенные поля');
        } else {
            $('#postform').submit();
            //alert('OK');
        }

    }    
$(document).ready(function () {

    
    
function getBase64(elem, file) {
        var reader = new FileReader();
        reader.readAsDataURL(file);
        var container = $(elem).parent().find('label').first();
        reader.onload = function() {
            console.log(reader.result);


            container.css('background-image', 'url(' + reader.result + ')');
            container.css('background-repeat', 'no-repeat');
            container.css('background-size', 'cover');
            return reader.result;
        };
        reader.onloadstart = function(event) {

            container.css('background-image', 'url("/img/spinload.svg")');
            container.css('background-repeat', 'no-repeat');
            container.css('background-size', 'cover');

        };

        reader.onerror = function(error) {
            console.log('Error: ', error);
            return null;
        };

    }

$(".delete-item").click(function() {
    var id = $(this).attr("data-id");
    var el = $(this).parent();
    $.ajax({
        url: '/ajax/delete_portfolio',
        method: 'get',
        dataType: 'html',
        data: {id: id},
        success: function(data){
            data = JSON.parse(data);
            if(data.status == 'OK') {
                el.remove();
            } else {
                alert("Произошла ошибка! Обратитесь к администрации!");
            }
        }
    });
});

$('input[type="file"]').on("change", function() {
            var id = this.id
            console.log("click id=" + id);
            var file = this.files[0];
            var data = getBase64(this, file);
            //encodeImgtoBase64(this);

            //this.src=data;


        });
 });
 </script>
        <?$this->load->view("templates/cabinet/fotter_view")?>
