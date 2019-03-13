<ul class="nav navbar-nav">

  <?php 
        $usuario = Auth::user();
  
        $roleId = $usuario->role_id; 
        $permisosUsuario = $usuario->roles->pluck('name')->toArray();
        $permisoRh = in_array('rh', $permisosUsuario);
        $permisoJefe = $usuario->misEmpleados->count();
  ?>
  
@php
    if (Voyager::translatable($items)) {
        $items = $items->load('translations');
    }
@endphp

@foreach ($items as $item)
    @php
        $listItemClass = [];
        $styles = null;
        $linkAttributes = null;
        $transItem = $item;

        if (Voyager::translatable($item)) {
            $transItem = $item->translate($options->locale);
        }

        $href = $item->link();

        // Current page
        if(url($href) == url()->current()) {
            array_push($listItemClass, 'active');
        }

        $permission = '';
        $hasChildren = false;

        // With Children Attributes
        if(!$item->children->isEmpty())
        {
            foreach($item->children as $child)
            {
                $hasChildren = $hasChildren || Auth::user()->can('browse', $child);

                if(url($child->link()) == url()->current())
                {
                    array_push($listItemClass, 'active');
                }
            }
            if (!$hasChildren) {
                continue;
            }

            $linkAttributes = 'href="#' . $transItem->id .'-dropdown-element" data-toggle="collapse" aria-expanded="'. (in_array('active', $listItemClass) ? 'true' : 'false').'"';
            array_push($listItemClass, 'dropdown');
        }
        else
        {
            $linkAttributes =  'href="' . url($href) .'"';

            if(!Auth::user()->can('browse', $item)) {
                continue;
            }
        }
    @endphp
    
@if( $transItem->title != 'Admin' || $roleId == 1 )  
    <li class="{{ implode(" ", $listItemClass) }}">
      
        <a {!! $linkAttributes !!} target="{{ $item->target }}" }}">
                                                                   
          <span class="icon lateral {{ $item->icon_class }}"lateral ></span>
          <span class="title">{{ $transItem->title }}
          
          @if($transItem->title == 'Recursos Humanos')
             @php
                
                $menu = array();
                
                //Lo ven todos
                $todos = $item->children->last();
                
                //Lo ve solo rh
                if($permisoRh) {
                   $menu[] = $item->children->firstWhere('title', "Reporte A.P.");
                   $menu[] = $item->children->firstWhere('title', 'Reporte de Ev de Desempeño');
                }
                
                //Lo ve solo el que tiene empleados
                if($permisoJefe){
                   $menu[] = $item->children->firstWhere('title', 'Evaluaciones de Desempeño');
                }
                
                $menu[] = $todos;
                $item->children = $menu;
            @endphp
            
          @endif
      
      </span>
        </a>
        @if($hasChildren)
            <div id="{{ $transItem->id }}-dropdown-element" class="panel-collapse collapse {{ (in_array('active', $listItemClass) ? 'in' : '') }}">
                <div class="panel-body">
                    @include('voyager::menu.admin_menu', ['items' => $item->children, 'options' => $options, 'innerLoop' => true])
                </div>
            </div>
        @endif
    </li>
@endif

@endforeach

</ul>