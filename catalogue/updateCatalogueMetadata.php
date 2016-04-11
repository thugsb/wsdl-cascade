<?php
date_default_timezone_set('America/New_York');
$title = 'Test';

// $type_override = 'page';
// $start_asset = 'e59cc45a7f00000100c46dcf503b2144';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
    return true;
}
function foldertest($child) {
    return true;
}
function edittest($asset) {
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed, $total;
  $changed = false;

  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "level") {
      if (is_array($dyn->fieldValues->fieldValue) ) {
        $level = 'array';

        if (count($dyn->fieldValues->fieldValue) == 2) {
          $obji = new StdClass;
          $obji->value = 'Intermediate';
          $obja = new StdClass;
          $obja->value = 'Advanced';
          $objs = new StdClass;
          $objs->value = 'Sophomore and above';
          $objo = new StdClass;
          $objo->value = 'Open';
          $objl = new StdClass;
          $objl->value = 'Lecture';
          $objss = new StdClass;
          $objss->value = 'Small seminar';

          if ( in_array($obji, $dyn->fieldValues->fieldValue) && in_array($obja, $dyn->fieldValues->fieldValue)) {
            $dyn->fieldValues->fieldValue = new StdClass;
            $dyn->fieldValues->fieldValue->value = 'Intermediate/Advanced';
            $changed = true;
          }

          if ( in_array($objs, $dyn->fieldValues->fieldValue) && in_array($obja, $dyn->fieldValues->fieldValue)) {
            $dyn->fieldValues->fieldValue = new StdClass;
            $dyn->fieldValues->fieldValue->value = 'Intermediate/Advanced';
            $changed = true;
          }

          if ( in_array($objo, $dyn->fieldValues->fieldValue) && in_array($obji, $dyn->fieldValues->fieldValue) ) {
            $dyn->fieldValues->fieldValue = new StdClass;
            $dyn->fieldValues->fieldValue->value = 'Open';
            $changed = true;
          }

          if ( in_array($objo, $dyn->fieldValues->fieldValue) && in_array($objl, $dyn->fieldValues->fieldValue) ) {
            $dyn->fieldValues->fieldValue = new StdClass;
            $dyn->fieldValues->fieldValue->value = 'Open';
            $changed = true;
          }

          if ( in_array($objo, $dyn->fieldValues->fieldValue) && in_array($objss, $dyn->fieldValues->fieldValue) ) {
            $dyn->fieldValues->fieldValue = new StdClass;
            $dyn->fieldValues->fieldValue->value = 'Open';
            $changed = true;
          }

          if ( in_array($obji, $dyn->fieldValues->fieldValue) && in_array($objl, $dyn->fieldValues->fieldValue) ) {
            $dyn->fieldValues->fieldValue = new StdClass;
            $dyn->fieldValues->fieldValue->value = 'Intermediate';
            $changed = true;
          }

          if ( in_array($obji, $dyn->fieldValues->fieldValue) && in_array($objs, $dyn->fieldValues->fieldValue) ) {
            $dyn->fieldValues->fieldValue = new StdClass;
            $dyn->fieldValues->fieldValue->value = 'Intermediate';
            $changed = true;
          }

          if ( in_array($obji, $dyn->fieldValues->fieldValue) && in_array($objss, $dyn->fieldValues->fieldValue) ) {
            $dyn->fieldValues->fieldValue = new StdClass;
            $dyn->fieldValues->fieldValue->value = 'Intermediate';
            $changed = true;
          }

          if ( in_array($obja, $dyn->fieldValues->fieldValue) && in_array($objss, $dyn->fieldValues->fieldValue) ) {
            $dyn->fieldValues->fieldValue = new StdClass;
            $dyn->fieldValues->fieldValue->value = 'Advanced';
            $changed = true;
          }
        }

        if (count($dyn->fieldValues->fieldValue) > 1) {
          echo "<div class='f'>WARNING: This course has multiple levels. <strong>";
          foreach ($dyn->fieldValues->fieldValue as $key => $value) {
            echo $value->value . ' + ';
          }
          echo '</strong></div>';
          $total['f']++;
        }

      } else {
        $level = $dyn->fieldValues->fieldValue->value;
        if ($level == 'FYS' || $level == 'Lecture') {
          $dyn->fieldValues->fieldValue->value = 'Open';
          $changed = true;
        }
      }
    }
    if ($dyn->name == "semester") {
      $semester = $dyn->fieldValues->fieldValue->value;
    }
    if ($dyn->name == "course-type") {
      $courseType = $dyn->fieldValues->fieldValue->value;
    }
  }

  if ($semester == 'FYS' && $level == 'FYS' && $courseType == 'Seminar') {
    foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
      if ($dyn->name == "course-type") {
        $dyn->fieldValues->fieldValue->value = 'FYS';
        $changed = true;
        $courseType = 'FYS';
      }
    }
  }

  if ($semester == 'FYS' && $semester != $courseType) {
    echo "<div class='f'>WARNING a: The Semester is $semester but the Course Type is $courseType (they should match).</div>";
    $total['f']++;
  }
  if ($semester == 'Component' && $semester != $courseType) {
    echo "<div class='f'>WARNING b: The Semester is $semester but the Course Type is $courseType (they should match).</div>";
    $total['f']++;
  }
  if ($level == 'Lecture') {
    if ($courseType != 'Lecture' && $courseType != 'Small Lecture') {
      echo "<div class='f'>WARNING c: The Level is $level but the Course Type is $courseType (the course type should be Lecture or Small Lecture.</div>";
      $total['f']++;
    }
  }
  if ($level == 'FYS' && $courseType != 'FYS') {
    echo "<div class='f'>WARNING d: The Level is $level but the Course Type is $courseType (they should match).</div>";
    $total['f']++;
  }
  if ($level == 'Small seminar' && $courseType != 'Small seminar') {
    echo "<div class='f'>WARNING e: The Level is $level but the Course Type is $courseType (they should match).</div>";
    $total['f']++;
  }

  if ($semester == 'FYS' || $semester == 'Fall/Spring' || $semester == 'Component') {
    foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
      if ($dyn->name == "semester") {
        $dyn->fieldValues->fieldValue->value = 'None';
        $changed = true;
        $semester = 'None';
      }
    }
  }
  echo "Semester: $semester, Course Type: $courseType Level: $level";
}

if (!$cron) {include('../header.php');}

?>