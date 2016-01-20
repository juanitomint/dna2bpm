<?php

// var_dump($groups);

?>
<ul class="list-group" id="widget_groups">
{groups}
  <li class="list-group-item" data-idgroup="{idgroup}" >
        <span class="pull-right color_picker" >
           <button type="button" class="btn btn-xs btn-link dropdown-toggle" data-toggle="dropdown"> {first_color_anchor}</button>
            <ul class="dropdown-menu" >
               {ul}
            </ul>
        </span>
        <span class="pull-right button_hide">{eye}</span>
    {name}  
  </li>
{/groups}
</ul>

