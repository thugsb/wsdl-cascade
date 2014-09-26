<?php
$title = 'Copy Faculty Structured Data to Metadata';

// $type_override = 'page';
$start_asset = '2891e3f87f00000101b7715d1ba2a7fb';

function pagetest($child) {
  if (isset($_GET['name'])) {
    if (preg_match("/^".$_GET['name']."[a-z]/",$child->path->path)) {
      return true;
    }
  } else {
    if ($child->path->path != 'index' && preg_match('/^[a-z]/',$child->path->path)) {
      return true;
    }
  }
}
function foldertest($child) {
  return false;
}
function edittest($asset) {
  return true;
}


function changes(&$asset) {
  global $changed, $data;
  $changed = false;
  // Grab the correct fields
  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode[0]->structuredDataNodes->structuredDataNode as $sdnode) {
    if ($sdnode->identifier == "first") {
      $fac_first = $sdnode;
    } elseif ($sdnode->identifier == "last") {
      $fac_last = $sdnode;
    } elseif ($sdnode->identifier == "title") {
      $fac_title = $sdnode;
    } elseif ($sdnode->identifier == "email") {
      $fac_email = $sdnode;
    } elseif ($sdnode->identifier == "phone") {
      $fac_phone = $sdnode;
    } elseif ($sdnode->identifier == "status") {
      $fac_status = $sdnode;
    } elseif ($sdnode->identifier == "note") {
      $fac_note = $sdnode;
    } elseif ($sdnode->identifier == "content") {
      $fac_content = $sdnode;
    }
  }
  
  
  // Affiliations
  if (!is_array($asset["structuredData"]->structuredDataNodes->structuredDataNode[1]->structuredDataNodes->structuredDataNode)) {
    $asset["structuredData"]->structuredDataNodes->structuredDataNode[1]->structuredDataNodes->structuredDataNode = array($asset["structuredData"]->structuredDataNodes->structuredDataNode[1]->structuredDataNodes->structuredDataNode);
  }
  $links = array('site://','site://','site://','site://');
  for ($i = 0;$i <= count($asset["structuredData"]->structuredDataNodes->structuredDataNode[1]->structuredDataNodes->structuredDataNode)-1; $i++) {
    $link = 'site://'.str_replace(':','/',$asset["structuredData"]->structuredDataNodes->structuredDataNode[1]->structuredDataNodes->structuredDataNode[$i]->pagePath);
    $links[$i] = $link;
  }
  // print_r($links);
  
  // This is what we have grabbed
  // echo '<div>First: '.$fac_first->text.'<br>Last: '.$fac_last->text.'<br>Title: '.$fac_title->text.'<br>Email: '.$fac_email->text.'<br>Status: '.$fac_status->text.'<br>Note: '.$fac_note->text.'<br>Bio: '.$fac_content->text.'</div>';
  
  // Set the data in the correct metadata fields
  if ($asset["metadata"]->teaser != $fac_content->text) {
    $asset["metadata"]->teaser = $fac_content->text; // WYSIWYG Bio Content
    $changed = true;
  }
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "first") {
      $first_name = $fac_first->text;
      if ($dyn->fieldValues->fieldValue->value != $fac_first->text) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $fac_first->text;
    } elseif ($dyn->name == "last") {
      $last_name = $fac_last->text;
      if ($dyn->fieldValues->fieldValue->value != $fac_last->text) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $fac_last->text;
    } elseif ($dyn->name == "faculty-title") {
      if ($dyn->fieldValues->fieldValue->value != $fac_title->text) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $fac_title->text;
    } elseif ($dyn->name == "email") {
      if ($dyn->fieldValues->fieldValue->value != $fac_email->text) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $fac_email->text;
    } elseif ($dyn->name == "phone") {
      if ($dyn->fieldValues->fieldValue->value != $fac_phone->text) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $fac_phone->text;
    } elseif ($dyn->name == "status") {
      if ($dyn->fieldValues->fieldValue->value != $fac_status->text) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $fac_status->text;
    } elseif ($dyn->name == "note") {
      if ($dyn->fieldValues->fieldValue->value != $fac_note->text) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $fac_note->text;
    } elseif ($dyn->name == "affiliation-link-1") {
      if ($dyn->fieldValues->fieldValue->value != $links[0] && $links[0] != 'site://') {
        $changed = true;
        $dyn->fieldValues->fieldValue->value = $links[0];
      }
    } elseif ($dyn->name == "affiliation-display-1") {
      if (is_array($dyn->fieldValues->fieldValue)) {$dyn->fieldValues->fieldValue = new stdClass;}
      if ($dyn->fieldValues->fieldValue->value != $data[$links[0]]) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $data[$links[0]];
    } elseif ($dyn->name == "affiliation-link-2") {
      if ($dyn->fieldValues->fieldValue->value != $links[1] && $links[1] != 'site://') {
        $changed = true;
        $dyn->fieldValues->fieldValue->value = $links[1];
      }
    } elseif ($dyn->name == "affiliation-display-2") {
      if (is_array($dyn->fieldValues->fieldValue)) {$dyn->fieldValues->fieldValue = new stdClass;}
      if ($dyn->fieldValues->fieldValue->value != $data[$links[1]]) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $data[$links[1]];
    } elseif ($dyn->name == "affiliation-link-3") {
      if ($dyn->fieldValues->fieldValue->value != $links[2] && $links[2] != 'site://') {
        $changed = true;
        $dyn->fieldValues->fieldValue->value = $links[2];
      }
    } elseif ($dyn->name == "affiliation-display-3") {
      if (is_array($dyn->fieldValues->fieldValue)) {$dyn->fieldValues->fieldValue = new stdClass;}
      if ($dyn->fieldValues->fieldValue->value != $data[$links[2]]) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $data[$links[2]];
    } elseif ($dyn->name == "affiliation-link-4") {
      if ($dyn->fieldValues->fieldValue->value != $links[3] && $links[3] != 'site://') {
        $changed = true;
        $dyn->fieldValues->fieldValue->value = $links[3];
      }
    } elseif ($dyn->name == "affiliation-display-4") {
      if (is_array($dyn->fieldValues->fieldValue)) {$dyn->fieldValues->fieldValue = new stdClass;}
      if ($dyn->fieldValues->fieldValue->value != $data[$links[3]]) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $data[$links[3]];
    }
  }
  $full_name = $first_name.' '.$last_name;
  if ($asset['metadata']->title != $full_name) {
    $changed = true;
    $asset['metadata']->title = $full_name;
  }
}


$data = array (
  'site://www.slc.edu+catalogue/clusters/africana-studies/index' => 'Africana Studies',
  'site://www.slc.edu+catalogue/clusters/cognitive-and-brain-science/index' => 'Cognitive and Brain Science',
  'site://www.slc.edu+catalogue/clusters/design-studies/index' => 'Architecture and Design',
  'site://www.slc.edu+catalogue/clusters/development-studies/index' => 'Development Studies',
  'site://www.slc.edu+catalogue/clusters/ethnic-and-diasporic-studies/index' => 'Ethnic and Diasporic Studies',
  'site://www.slc.edu+catalogue/clusters/filmmaking-screenwriting-media-arts/index' => 'Filmmaking, Screenwriting and Media Arts',
  'site://www.slc.edu+catalogue/clusters/games-interactive-media/index' => 'Games, Interactivity, and Playable Media',
  'site://www.slc.edu+catalogue/clusters/global-studies/index' => 'Global Studies',
  'site://www.slc.edu+catalogue/clusters/health-science-society/index' => 'Health, Science and Society',
  'site://www.slc.edu+catalogue/clusters/international-studies/index' => 'International Studies',
  'site://www.slc.edu+catalogue/clusters/lals/index' => 'Latin American and Latino/a Studies',
  'site://www.slc.edu+catalogue/clusters/lesbian-gay-bisexual-and-transgender-studies/index' => 'Lesbian, Gay, Bisexual, and Transgender Studies',
  'site://www.slc.edu+catalogue/clusters/middle-eastern-and-islamic-studies/index' => 'Middle Eastern and Islamic Studies',
  'site://www.slc.edu+catalogue/clusters/political-economy/index' => 'Political Economy',
  'site://www.slc.edu+catalogue/clusters/pre-health/index' => 'Pre-health',
  'site://www.slc.edu+catalogue/clusters/urban-studies/index' => 'Urban Studies',
  'site://www.slc.edu+catalogue/clusters/womens-studies/index' => "Women's Studies",
  'site://www.slc.edu+catalogue/creative-and-performing-arts/dance/index' => 'Dance',
  'site://www.slc.edu+catalogue/creative-and-performing-arts/music/index' => 'Music',
  'site://www.slc.edu+catalogue/creative-and-performing-arts/theatre/index' => 'Theatre',
  'site://www.slc.edu+catalogue/creative-and-performing-arts/visual-arts/index' => 'Visual Arts',
  'site://www.slc.edu+catalogue/creative-and-performing-arts/writing/index' => 'Writing',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/anthropology/index' => 'Anthropology',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/asian-studies/index' => 'Asian Studies',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/economics/index' => 'Economics',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/environmental-studies/index' => 'Environmental Studies',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/geography/index' => 'Geography',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/history/index' => 'History',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/politics/index' => 'Politics',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/psychology/index' => 'Psychology',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/public-policy/index' => 'Public Policy',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/science-technology-and-society/index' => 'Science, Technology and Society',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/sociology/index' => 'Sociology',
  'site://www.slc.edu+catalogue/history-and-the-social-sciences/sts/index' => 'Science, Technology, and Society',
  'site://www.slc.edu+catalogue/humanities/art-history/index' => 'Art History',
  'site://www.slc.edu+catalogue/humanities/film-history/index' => 'Film History',
  'site://www.slc.edu+catalogue/humanities/languages-and-literatures/index' => 'Modern Languages and Literatures',
  'site://www.slc.edu+catalogue/humanities/languages-and-literatures/chinese/index' => 'Chinese',
  'site://www.slc.edu+catalogue/humanities/languages-and-literatures/classics/index' => 'Classics',
  'site://www.slc.edu+catalogue/humanities/languages-and-literatures/french/index' => 'French',
  'site://www.slc.edu+catalogue/humanities/languages-and-literatures/german/index' => 'German',
  'site://www.slc.edu+catalogue/humanities/languages-and-literatures/greek/index' => 'Greek',
  'site://www.slc.edu+catalogue/humanities/languages-and-literatures/italian/index' => 'Italian',
  'site://www.slc.edu+catalogue/humanities/languages-and-literatures/japanese/index' => 'Japanese',
  'site://www.slc.edu+catalogue/humanities/languages-and-literatures/latin/index' => 'Latin',
  'site://www.slc.edu+catalogue/humanities/languages-and-literatures/russian/index' => 'Russian',
  'site://www.slc.edu+catalogue/humanities/languages-and-literatures/spanish/index' => 'Spanish',
  'site://www.slc.edu+catalogue/humanities/literature/index' => 'Literature',
  'site://www.slc.edu+catalogue/humanities/philosophy/index' => 'Philosophy',
  'site://www.slc.edu+catalogue/humanities/religion/index' => 'Religion',
  'site://www.slc.edu+catalogue/natural-sciences-and-mathematics/biology/index' => 'Biology',
  'site://www.slc.edu+catalogue/natural-sciences-and-mathematics/chemistry/index' => 'Chemistry',
  'site://www.slc.edu+catalogue/natural-sciences-and-mathematics/computer-science/index' => 'Computer Science',
  'site://www.slc.edu+catalogue/natural-sciences-and-mathematics/mathematics/index' => 'Mathematics',
  'site://www.slc.edu+catalogue/natural-sciences-and-mathematics/physics/index' => 'Physics',
  'site://www.slc.edu+catalogue/disciplines/science-and-mathematics/index' => 'Science and Mathematics',
  'site://www.slc.edu+catalogue/disciplines/social-science/index' => 'Social Science',
  'site://www.slc.edu+grad-catalogue/art-of-teaching/index' => 'MS Ed Art of Teaching Program',
  'site://www.slc.edu+grad-catalogue/child-development/index' => 'MA Child Development Program',
  'site://www.slc.edu+grad-catalogue/child-development/index' => 'MA/MSW Dual Degree in Social Work and Child Development',
  'site://www.slc.edu+grad-catalogue/dance/index' => 'MFA Dance Program',
  'site://www.slc.edu+grad-catalogue/dmt/index' =>'MS Dance/Movement Therapy Program',
  'site://www.slc.edu+grad-catalogue/dance-movement-therapy/index' => 'MS Dance/Movement Therapy Program',
  'site://www.slc.edu+grad-catalogue/health-advocacy/index' => 'MA Health Advocacy Program',
  'site://www.slc.edu+grad-catalogue/human-genetics/index' => 'MS Human Genetics Program',
  'site://www.slc.edu+grad-catalogue/theatre/index' => 'MFA Theatre Program',
  'site://www.slc.edu+grad-catalogue/womens-history/index' => "MA Women's History Program",
  'site://www.slc.edu+grad-catalogue/writing/index' => 'MFA Writing Program',
  'site://' => '',
  'site://www.slc.edu+catalogue/index' => ''
);


if (!$cron)
  include('../header.php');

?>