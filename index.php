<?php

$m=''; //for error messages
$id_event=''; //id event created 
$link_event; 
if(isset($_POST['register'])){
    

    date_default_timezone_set('America/argentina/buenos_aires');
    include_once 'https://www.googleapis.com/calendar/v3/vendor/autoload.php';

    //configurar variable de entorno / set enviroment variable
    //intrudicr tus propias credenciales
    putenv('GOOGLE_APPLICATION_CREDENTIALS= credenciales');

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(['https://www.googleapis.com/auth/calendar']);

    //define id calendario
    $id_calendar='';//
    
   
      
    $datetime_start = new DateTime($_POST['fecha']);
    $datetime_end = new DateTime($_POST['fecha']);
    
    //aumentamos treinta minutos a la hora inicial/ para aumentar horas se usa H en vez de M
    $time_end = $datetime_end->add(new DateInterval('PT30M'));
    
    //datetime must be format RFC3339
    $time_start =$datetime_start->format(\DateTime::RFC3339);
    $time_end=$time_end->format(\DateTime::RFC3339);

    
    $nombre=(isset($_POST['nombre']))?$_POST['nombre']:' xyz ';
    $apellido=(isset($_POST['apellido']))?$_POST['apellido']:' xyz ';
    $dni=(isset($_POST['dni']))?$_POST['dni']:' xyz ';
    $edad=(isset($_POST['edad']))?$_POST['edad']:' xyz ';
    $celular=(isset($_POST['cel']))?$_POST['cel']:' xyz ';
    try{
        
        //instanciamos el servicio
    	 $calendarService = new Google_Service_Calendar($client);
      
        
      
        //parámetros para buscar eventos en el rango de las fechas del nuevo evento
        //params to search events in the given dates
        $optParams = array(
            'orderBy' => 'startTime',
            'maxResults' => 20,
            'singleEvents' => TRUE,
            'timeMin' => $time_start,
            'timeMax' => $time_end,
        );

        //obtener eventos 
        $events=$calendarService->events->listEvents($id_calendar,$optParams);
        
        //obtener número de eventos / get how many events exists in the given dates
        $cont_events=count($events->getItems());
     
        //crear evento si no hay eventos / create event only if there is no event in the given dates
        if($cont_events < 6){

            $event = new Google_Service_Calendar_Event();
            $event->setSummary("Turno reservado por: $nombre $apellido");
            $event->setDescription("Su Dni es: $dni y tiene $edad años y su numero de celular es: $celular");

            //fecha inicio
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime($time_start);
            $event->setStart($start);

            //fecha fin
            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime($time_end);
            $event->setEnd($end);

          
            $createdEvent = $calendarService->events->insert($id_calendar, $event);
            $id_event= $createdEvent->getId();
            $link_event= $createdEvent->gethtmlLink();
            
        }else{
            $m = "Hay ".$cont_events." eventos en ese rango de fechas";
        }


    }catch(Google_Service_Exception $gs){
     
      $m = json_decode($gs->getMessage());
      $m= $m->error->message;

    }catch(Exception $e){
        $m = $e->getMessage();
      
    }
}





?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Form</title>
     <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script type="text/javascript" src="moment.js"></script>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
      <link rel="stylesheet" href="prueba_eric.css">
</head>
<body class="responsivo">
<header>
  <p>&copy; 2020 Desarrollado por <strong> Eric Guzman </strong></p>
</header>

<div class="container d-flex justify-content-center">
  <div class="row">
    <div class="col-md-12 card">
    <h1>Reservacion de turnos</h1>
  <form action="" method="POST">
    <?php 
    if(isset($_POST['register'])){
      if($m!=''){
      ?>
      <script>
                alert("Error: <?php echo $m; ?>");
                window.location= 'index.php'
        </script>
      <?php
      }
      elseif($id_event!=''){
        echo "<script>
                alert('El turno se solicito con exito');
                window.location= 'index.php'
        </script>";
      }

    }
    else{
    ?>


<div class="card-body">
    <div class="form-group">
    <label for="name">Nombre</label>
    <input class="form-control" type="text" name="nombre" id="name" placeholder="Nombre" required>
  </div>
  <div class="form-group">
  <label for="apellidos">Nombre</label>
  <input class="form-control" type="text" id="apellidos" name="apellido" placeholder="Apellido" required>
  </div>
  <div class="form-group">
  <label for="dnis">Dni</label>
    <input class="form-control" type="number" name="dni" id="dnis" placeholder="Dni" required>
  </div>
  <div class="form-group">
  <label for="edads">Edad</label>
    <input class="form-control" type="number" name="edad" id="edads" placeholder="Edad" required>
  </div>
  <div class="form-group">
  <label for="cels">Celular</label>
    <input class="form-control" type="tel" name="cel" id="cels" placeholder="Numero de celular" required>
  </div>
  <div class="form-group">
  <label for="fechas">Turno a solicitar</label>
        <select class="form-control" name="fecha" id="fechas" required>
          <option value="07-11-2020 08:00">08:00</option>
          <option value="07-11-2020 08:30">08:30</option>
          <option value="07-11-2020 09:00">09:00</option>
          <option value="07-11-2020 09:30">09:30</option>
          <option value="07-11-2020 10:00">10:00</option>
          <option value="07-11-2020 10:30">10:30</option>
          <option value="07-11-2020 11:00">11:00</option>
          <option value="07-11-2020 11:30">11:30</option>
          <option value="07-11-2020 12:00">12:30</option>
          <option value="07-11-2020 12:30">12:30</option>
        </select>
  </div>
  <button type="submit" class="btn btn-primary" name="register">Enviar</button>
</div>

            

    

   <?php
    }
    ?>
</form>
</div>
  </div>
</div>

<script>
            function reload(){
              location.href="http://localhost/sistema_turnos/index.php";
            }

</script>
</body>
</html>