<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* ---WF-Tasks------ */
$lang["MyTasks"] = 'Mis Tareas';
$lang["byGroup"] = 'Por Grupo';
$lang["byStatus"] = 'Por Estado';
$lang["Pending"] = 'Pendientes';
$lang["Finished"] = 'Finalizadas';
$lang["Canceled"] = 'Canceladas';
$lang["Waiting"] = 'Esperando';
$lang["Stoped"] = 'Detenida';
$lang["finishTask"] = 'dar por Finalizada';
$lang["closeTask"] = 'Volver';
$lang["dismissTask"] = 'Luego';
$lang["claim"] = 'reclamar';
$lang["refuse"] = 'rechazar';
$lang["delegate"] = 'delegar';
$lang["task"] = 'tarea';

/* --WF-Messages----- */
$lang['newTask'] = 'Nueva Tarea';
$lang['total'] = 'Total';
$lang['inProgress'] = 'En Progreso';
$lang['newTaskBody'] = "{from_user name} {from_user lastname} te ha asignado una nueva tarea: {shape name}
    <br/>
    Haz click <a href='{basedir}/bpm/engine/run/model/{idwf}/{idcase}'>>>>aqu�<<<</a> para completarla";
$lang['lock'] = 'Bloqueo';
$lang['message'] = 'Mensaje';
$lang["caseLocked"] = "<i class='icon-lock'></i> Este caso hs sido bloqueado por:<br/> {user_lock}<br/> desde el: {time}";
$lang["taskLocked"] = "<i class='icon-lock'></i> Esta tarea ha sido bloqueada por:<br/> {user_lock}<br/> desde el: {time}";
$lang["noMoreTasks"] = "Ud no tiene más tareas pendientes por ahora";
$lang["caseClosed"] = "El proceso ha finalizado.";
$lang["closedCases"] = "Casos Cerrados";
/* --WF-conditions----- */
$lang['true'] = 'Sí';
$lang['false'] = 'No';
/* --File ---*/
$lang['drop_here']="Arrastre sus archivos aquí";