<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>404 Pagina no encontrada</title>

	<style>

.bg-image {
    background-image: url('../../images/madera.jpg');
    background-size: cover;
    background-position: center;
  }
  .bg-image2 {
    background-image: url('../../../../public/images/madera.jpg');
    background-size: cover;
    background-position: center;
  }

		div.logo {
			height: 200px;
			width: 155px;
			display: inline-block;
			opacity: 0.08;
			position: absolute;
			top: 2rem;
			left: 50%;
			margin-left: -73px;
		}
		body {
			height: 100%;
			background: #fafafa;
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			color: #777;
			font-weight: 300;
		}
		h1 {
			font-weight: lighter;
			letter-spacing: 0.8;
			font-size: 3rem;
			margin-top: 0;
			margin-bottom: 0;
			color: #222;
		}
		.wrap {
			max-width: 1024px;
			margin: 5rem auto;
			padding: 2rem;
			background: #fff;
			text-align: center;
			border: 1px solid #efefef;
			border-radius: 0.5rem;
			position: relative;
		}
		pre {
			white-space: normal;
			margin-top: 1.5rem;
		}
		code {
			background: #fafafa;
			border: 1px solid #efefef;
			padding: 0.5rem 1rem;
			border-radius: 5px;
			display: block;
		}
		p {
			margin-top: 1.5rem;
		}
		.footer {
			margin-top: 2rem;
			border-top: 1px solid #efefef;
			padding: 1em 2em 0 2em;
			font-size: 85%;
			color: #999;
		}
		a:active,
		a:link,
		a:visited {
			color: #dd4814;
		}

        .card {
        border-radius: 30px;
    }

        button {
        border-top-left-radius: 40px 40px;
        border-top-right-radius: 40px 40px;
        border-bottom-right-radius: 40px 40px;
        border-bottom-left-radius: 40px 40px;
        width: 100%;
        box-shadow: none;
        font-size: 20px;
        border: none;

    }



     .form-control {
        border: 2px solid #c1c1c17a !important;
        border-radius: 30px !important;
        min-width:350px !important;

    }


/*  Agregado */
    .alert-success {
    color: #0f5132;
    background-color: #d1e7dd;
    border-color: #badbcc;
    border-radius:30px;
    text-align:center;
    font-size:1.2em;
}
/* -------------*/

    .imagen_registro_3{
    border-top-left-radius: 30px 30px;
    border-bottom-left-radius: 30px 30px;
    border-top-right-radius: 30px 30px;
    border-bottom-right-radius: 30px 30px;


}
 .btn{
      border-top-left-radius: 30px 30px;
    border-bottom-left-radius: 30px 30px;
    border-top-right-radius: 30px 30px;
    border-bottom-right-radius: 30px 30px;
    background-color: #898266 !important;
    color:white;

 }

        </style>
</head>
<body>
	<div class="wrap">


  <div class="row g-0 form_registro justify-content-start  bg-image2" >

<div class="col"> </div>

   <div class="col-sm ">
     <div class="login d-flex align-items-center py-5">


       <div class="container bg-white  imagen_registro_3">

         <div class="row  p-4">
           <div class="col-sm  p-2 ">
           <img src="\images\logo\logos_facturas_2.png" width="350" height="110" alt="logo" class="mt-2"><br>


               <h1>404 - Archivo no encontrado</h1>

		<p>
			<?php if (! empty($message) && $message !== '(null)') : ?>
				¡Lo siento! Parece que no puede encontrar la página que estaba buscando.
			<?php else : ?>
				¡Lo siento! Parece que no puede encontrar la página que estaba buscando.
			<?php endif ?>
		</p>

                 </div>
         </div>
       </div>
     </div>

   </div>

     <div class="col">

     </div>
 </div>

</body>
</html>
