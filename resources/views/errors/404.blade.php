<!DOCTYPE html>
<html>
    <head>
        <title>404 - Página não encontrada</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    </head>
    <body>
        <section id="middle">
            <img src="{{ asset('assets/img/logo/logo_sm.png') }}" onerror="this.src = '{{ asset('assets/img/default/logo/logo_sm.png') }}'" class="logo">
            <div class="code">Página não encontrada</div>
            <div class="band">
                <h1>A página que tentou aceder não existe ou pode ter sido removida.</h1>
                <a href="{{ url()->previous() }}" class="button">Retroceder</a>
            </div>
        </section>
    </body>
</html>
<style>

    body{
        font-family: 'Open Sans', sans-serif;
        padding: 0;
        margin: 0;
        background-size: 200%;
        background-position-y: -100px;
        background-position-x: -100px;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
    .logo{
        width: 350px;
    }
    #middle{
        height: 290px;
        text-align: center;
        top: 50%;
        left: 0;
        right: 0;
        position: absolute;
        margin-top: -250px;
    }
    #top{
        top: 0;
        bottom: 300px;
        /*background: #FFFFFF;*/
    }

    #bottom{
        /*background: #672d8b;*/
        position: absolute;
        top: 50%;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: -1;
    }

    .band{
        margin-top: -50px;
        padding: 39px;
        /*background: #461b6e;*/
    }

    .code{
        font-size: 50px;
        margin-bottom: 30px;
        margin-top: 35px;
        /* color: #461b6e; */
        font-weight: 300;
    }
    h1{
        margin: -15px 0 40px 0;
        font-size: 20px;
        font-weight: 300;
        color: #666666;
    }
    h2{
        margin: 0 0 40px 0;
        padding: 0;
        font-weight: normal;
        font-size: 20px;
        text-transform: capitalize;
        color: #eee;
    }
    .button{
        text-decoration: none;
        text-transform: capitalize;
        font-size: 16px;
        font-weight: 300;
        padding: 15px 20px;
        border-radius: 0px;
        border: none;
        background: #EE7C00;
        color: #ffffff;
    }
    .button:hover{
        cursor: pointer;
        background: #EE7C00;
        opacity: 0.7;
    }
</style>
