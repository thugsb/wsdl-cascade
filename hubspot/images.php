<?php
date_default_timezone_set('GMT');

$hs = json_decode( file_get_contents('hs_pages.json') );

$imageNames = [];
foreach ($hs->pages as $i => $page) {
if ( $page->template_path == 'generated_layouts/3708457939.html' || $page->template_path == 'generated_layouts/3610973427.html' ) {
	if ($page->widget_containers->module_14283407904137244->widgets[0]->type == 'linked_image') {
      echo '<a href="'.$page->widget_containers->module_14283407904137244->widgets[0]->body->src.'" style="color:#090; display:block;">' . $page->widget_containers->module_14283407904137244->widgets[0]->body->src . '</a>';
    } elseif ($page->widget_containers->module_14283407904137244->widgets[0]->type == 'gallery') {
    	echo '<div>Gallery</div>';
    	foreach ($page->widget_containers->module_14283407904137244->widgets[0]->body->slides as $key => $slide) {
		      echo '<a href="'.$slide->img_src.'" style="color:#090; display:block;">' . $slide->img_src . '</a>';
    	}
    	echo '<div>End</div>';
    } elseif ($page->widget_containers->module_14283407904137244->widgets[0]->type == 'gallery') {
    	echo '<div>Gallery</div>';
    	foreach ($page->widget_containers->module_14283407904137244->widgets[0]->body->slides as $key => $slide) {
		      echo '<a href="'.$slide->img_src.'" style="color:#090; display:block;">' . $slide->img_src . '</a>';
    	}
    	echo '<div>End</div>';

    } else {
      echo '<div style="color:#f00">module_14283407904137244->widgets[0] is not a linked_image for page '. $page->url .'</div>';
    }
}
}