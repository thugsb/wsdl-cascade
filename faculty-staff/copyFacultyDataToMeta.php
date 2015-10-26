<?php
date_default_timezone_set('America/New_York');
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
  'site://www.sarahlawrence.edu+catalogue/africana-studies/index' => 'Africana Studies',
  'site://www.sarahlawrence.edu+catalogue/anthropology/index' => 'Anthropology',
  'site://www.sarahlawrence.edu+catalogue/architecture-and-design-studies/index' => 'Architecture and Design Studies',
  'site://www.sarahlawrence.edu+catalogue/art-history/index' => 'Art History',
  'site://www.sarahlawrence.edu+catalogue/asian-studies/index' => 'Asian Studies',
  'site://www.sarahlawrence.edu+catalogue/biology/index' => 'Biology',
  'site://www.sarahlawrence.edu+catalogue/chemistry/index' => 'Chemistry',
  'site://www.sarahlawrence.edu+catalogue/chinese/index' => 'Chinese',
  'site://www.sarahlawrence.edu+catalogue/classics/index' => 'Classics',
  'site://www.sarahlawrence.edu+catalogue/cognitive-and-brain-science/index' => 'Cognitive and Brain Science',
  'site://www.sarahlawrence.edu+catalogue/computer-science/index' => 'Computer Science',
  'site://www.sarahlawrence.edu+catalogue/dance/index' => 'Dance',
  'site://www.sarahlawrence.edu+catalogue/development-studies/index' => 'Development Studies',
  'site://www.sarahlawrence.edu+catalogue/economics/index' => 'Economics',
  'site://www.sarahlawrence.edu+catalogue/environmental-studies/index' => 'Environmental Studies',
  'site://www.sarahlawrence.edu+catalogue/ethnic-and-diasporic-studies/index' => 'Ethnic and Diasporic Studies',
  'site://www.sarahlawrence.edu+catalogue/film-history/index' => 'Film History',
  'site://www.sarahlawrence.edu+catalogue/filmmaking-screenwriting-and-media-arts/index' => 'Filmmaking, Screenwriting and Media Arts',
  'site://www.sarahlawrence.edu+catalogue/french/index' => 'French',
  'site://www.sarahlawrence.edu+catalogue/games-interactive-media/index' => 'Games, Interactivity, and Playable Media',
  'site://www.sarahlawrence.edu+catalogue/gender-and-sexuality-studies/index' => 'Gender and Sexuality Studies',
  'site://www.sarahlawrence.edu+catalogue/geography/index' => 'Geography',
  'site://www.sarahlawrence.edu+catalogue/german/index' => 'German',
  'site://www.sarahlawrence.edu+catalogue/global-studies/index' => 'Global Studies',
  'site://www.sarahlawrence.edu+catalogue/greek/index' => 'Greek',
  'site://www.sarahlawrence.edu+catalogue/health-science-and-society/index' => 'Health, Science and Society',
  'site://www.sarahlawrence.edu+catalogue/history/index' => 'History',
  'site://www.sarahlawrence.edu+catalogue/international-studies/index' => 'International Studies',
  'site://www.sarahlawrence.edu+catalogue/italian/index' => 'Italian',
  'site://www.sarahlawrence.edu+catalogue/japanese/index' => 'Japanese',
  'site://www.sarahlawrence.edu+catalogue/languages-and-literatures/index' => 'Modern Languages and Literatures',
  'site://www.sarahlawrence.edu+catalogue/latin/index' => 'Latin',
  'site://www.sarahlawrence.edu+catalogue/latin-american-and-latinoa-studies/index' => 'Latin American and Latino/a Studies',
  'site://www.sarahlawrence.edu+catalogue/lesbian-gay-bisexual-and-transgender-studies/index' => 'Lesbian, Gay, Bisexual, and Transgender Studies',
  'site://www.sarahlawrence.edu+catalogue/literature/index' => 'Literature',
  'site://www.sarahlawrence.edu+catalogue/mathematics/index' => 'Mathematics',
  'site://www.sarahlawrence.edu+catalogue/middle-eastern-and-islamic-studies/index' => 'Middle Eastern and Islamic Studies',
  'site://www.sarahlawrence.edu+catalogue/music/index' => 'Music',
  'site://www.sarahlawrence.edu+catalogue/philosophy/index' => 'Philosophy',
  'site://www.sarahlawrence.edu+catalogue/physics/index' => 'Physics',
  'site://www.sarahlawrence.edu+catalogue/political-economy/index' => 'Political Economy',
  'site://www.sarahlawrence.edu+catalogue/politics/index' => 'Politics',
  'site://www.sarahlawrence.edu+catalogue/pre-health-program/index' => 'Pre-health Program',
  'site://www.sarahlawrence.edu+catalogue/psychology/index' => 'Psychology',
  'site://www.sarahlawrence.edu+catalogue/public-policy/index' => 'Public Policy',
  'site://www.sarahlawrence.edu+catalogue/religion/index' => 'Religion',
  'site://www.sarahlawrence.edu+catalogue/russian/index' => 'Russian',
  'site://www.sarahlawrence.edu+catalogue/sts/index' => 'Science, Technology, and Society',
  'site://www.sarahlawrence.edu+catalogue/science-technology-and-society/index' => 'Science, Technology, and Society',
  'site://www.sarahlawrence.edu+catalogue/social-science/index' => 'Social Science',
  'site://www.sarahlawrence.edu+catalogue/sociology/index' => 'Sociology',
  'site://www.sarahlawrence.edu+catalogue/spanish/index' => 'Spanish',
  'site://www.sarahlawrence.edu+catalogue/theatre/index' => 'Theatre',
  'site://www.sarahlawrence.edu+catalogue/urban-studies/index' => 'Urban Studies',
  'site://www.sarahlawrence.edu+catalogue/visual-arts/index' => 'Visual Arts',
  'site://www.sarahlawrence.edu+catalogue/womens-studies/index' => "Women's Studies",
  'site://www.sarahlawrence.edu+catalogue/writing/index' => 'Writing',
  'site://www.sarahlawrence.edu+catalogue/science-and-mathematics/index' => 'Science and Mathematics',
  'site://www.sarahlawrence.edu+catalogue/social-science/index' => 'Social Science',
  'site://www.sarahlawrence.edu+grad-catalogue/art-of-teaching/index' => 'MS Ed Art of Teaching Program',
  'site://www.sarahlawrence.edu+grad-catalogue/child-development/index' => 'MA Child Development Program',
  'site://www.sarahlawrence.edu+grad-catalogue/dance-mfa/index' => 'MFA Dance Program',
  'site://www.sarahlawrence.edu+grad-catalogue/dmt/index' =>'MS Dance/Movement Therapy Program',
  'site://www.sarahlawrence.edu+grad-catalogue/dance-movement-therapy/index' => 'MS Dance/Movement Therapy Program',
  'site://www.sarahlawrence.edu+grad-catalogue/health-advocacy/index' => 'MA Health Advocacy Program',
  'site://www.sarahlawrence.edu+grad-catalogue/human-genetics/index' => 'MS Human Genetics Program',
  'site://www.sarahlawrence.edu+grad-catalogue/theatre-mfa/index' => 'MFA Theatre Program',
  'site://www.sarahlawrence.edu+grad-catalogue/womens-history/index' => "MA Women's History Program",
  'site://www.sarahlawrence.edu+grad-catalogue/writing-mfa/index' => 'MFA Writing Program',
  'site://' => '',
  'site://www.sarahlawrence.edu+catalogue/index' => ''
);


if (!$cron)
  include('../header.php');

?>