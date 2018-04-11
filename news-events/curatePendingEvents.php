<?php
date_default_timezone_set('America/New_York');
$title = 'Curate Events';

include_once('eventFolderIDs.php');

// $type_override = 'page';
$start_asset = $pending_folder;

$message .= 'NOTE: This page requires JavaScript. Optionally, use the GET argument "?date=yyyy-mm-dd" to filter by date, where dd and mm are not required. The "Read only" vs "Edit" radio options have no effect on this page.';
$htmlHead = "<link href='../lib/selectize.default.css' rel='stylesheet'/><script src='../lib/selectize.min.js'></script>";
$script = <<<EOS

$(function() {

  window.types = [
    "academics",
    "audiences",
    "themes",
    "sponsors",
    "channels",
    "faculty"
  ];
  window.academics = [
    "Humanities",
    "History and the Social Sciences",
    "Science and Mathematics",
    "Creative and Performing Arts",
    "Africana Studies",
    "Anthropology",
    "Art History",
    "Asian Studies",
    "Biology",
    "Chemistry",
    "Chinese",
    "Classics",
    "Cognitive and Brain Science",
    "Computer Science",
    "Dance",
    "Design Studies",
    "Development Studies",
    "Economics",
    "Environmental Studies",
    "Ethnic and Diasporic Studies",
    "Film History",
    "French",
    "Games, Interactive Art, and New Genres",
    "Gender and Sexuality Studies",
    "Geography",
    "German",
    "Greek",
    "Health, Science, and Society",
    "History",
    "International Studies",
    "Italian",
    "Japanese",
    "Latin",
    "Latin American and Latino/a Studies",
    "LGBT",
    "Literature",
    "Mathematics",
    "Middle Eastern and Islamic Studies",
    "Modern Languages and Literatures",
    "Music",
    "Philosophy",
    "Physics",
    "Political Economy",
    "Politics",
    "Pre-med",
    "Psychology",
    "Public Policy",
    "Religion",
    "Russian",
    "Science, Technology, and Society",
    "Sociology",
    "Spanish",
    "Theatre",
    "Urban Studies",
    "Visual and Studio Arts",
    "Digital Imagery",
    "Drawing",
    "Filmmaking and Moving Image Arts",
    "Painting",
    "Photography",
    "Printmaking",
    "Sculpture",
    "Women’s Studies",
    "Writing",
    "Art of Teaching",
    "Child Development",
    "MFA Dance",
    "Dance Movement Therapy",
    "Health Advocacy",
    "Human Genetics",
    "MFA Theatre",
    "Womens History",
    "MFA Writing",
    "Continuing Education",
    "Child Development Institute",
    "Early Childhood Center",
    "Special Programs",
    "Study Abroad",
    "Writing Institute"
  ];
  window.audiences = [
    "Administration",
    "Alumni",
    "Cristle Collins Judd",
    "Donors",
    "Faculty",
    "Friends of the College",
    "Graduate",
    "High School",
    "Karen R. Lawrence",
    "Neighbors",
    "Parents",
    "President",
    "Public",
    "Staff",
    "Students",
    "Undergraduate"
  ];
  window.themes = [
    "Academics",
    "Advancement",
    "Athletics",
    "BWCC",
    "Careers",
    "Commencement",
    "Graduate Admission",
    "Inauguration",
    "Library",
    "NYC and Local",
    "Reunion",
    "Student Life",
    "Undergraduate Admission"
  ];
  window.sponsors = [
    "Alumni Relations",
    "Career Services",
    "DAPS",
    "Real Talk",
    "SLAC"
  ];
  window.channels = [
    "WWW",
    "Homepage",
    "InTouch",
    "CURB"
  ]
  window.faculty = [
"abernethy-colin-d.",
"abraham-julie",
"abrams-samuel",
"abuba-ernest-h.",
"acting-faculty-",
"adams-jefferson",
"afzal-cameron-c.",
"agard-jones-vanessa",
"aggarwal-ujju",
"agresta-kirsten",
"alan-gilbert",
"albarelli-gerry",
"alexander-glenn",
"alexis-melissa",
"algire-andrew",
"ancona-marina",
"anderson-abraham",
"anderson-william",
"andriole-stephanie",
"anhalt-emily-katz",
"appel-cathy",
"arditi-neil",
"ash-erin",
"bajaj-komal",
"baker-damani",
"baker-nancy",
"balasubramaniam-tanjore",
"balkite-elisabeth-a.",
"barenboim-carl",
"barenboim-deanna",
"barnard-mary",
"basilio-jorge",
"beard-jo-ann",
"beavan-colin",
"begelman-igor",
"belcastro-sarah-marie",
"benzoni-stefania",
"berg-bruce",
"bernstein-david",
"biscardi-chester",
"blake-lorna-knowles",
"blalock-lucas",
"bosselaar-laure-anne",
"bower-bill-l.",
"bradshaw-patti",
"brand-roy",
"breitbart-vicki",
"brodzki-bella",
"brown-adam",
"brown-karen",
"brown-kurt",
"brown-wesley",
"buckley-jennifer",
"bukiet-melvin-jules",
"burnley-gary",
"calvin-scott",
"canick-jacob",
"carbon-lorayne",
"carstens-marie",
"carter-suzanne",
"castillo-mauricio",
"castriota-david",
"catanzaro-william",
"catterson-pat",
"chang-tina",
"chapman-susannah",
"charles-persis",
"cheng-eileen-ka-may",
"chen-mengyu",
"chen-priscilla",
"cho-john-song-pae",
"christle-heather",
"chung-una",
"clark-sarah",
"cleary-heather",
"cohen-rachel",
"confoy-kevin",
"conlan-kelli",
"corva-dominic",
"cottrell-margaret",
"cottrell-peggy",
"cramer-michael",
"cressman-drew-e.",
"cruz-cynthia",
"curulla-annelle",
"dance-faculty-",
"dasgupta-sayantani",
"datta-rhea",
"davis-michael",
"de-leon-cora",
"delne-claudy",
"de-sena-isabel",
"desjarlais-robert-r.",
"devine-emily",
"diamond-david",
"dillard-mary",
"ditkoff-beth-ann",
"dixon-jonathan",
"dizenko-natalia",
"dobyns-stephen",
"dodds-jerrilynn",
"dolan-siobhan",
"dollinger-roland",
"donzelli-aurora",
"downs-patrick",
"doyle-charlotte-l.",
"driscoll-kermit",
"drucker-jan",
"du-boff-jill",
"duce-scott",
"dufresne-angela",
"duggan-niamh",
"dumbutshena-rujeko",
"duran-nancy",
"durcan-judith",
"dynner-glenn",
"earle-jason",
"early-michael",
"eger-david-j.",
"ekman-june",
"ellicson-kirsten",
"ellis-matthew",
"ellis-thomas-sayers",
"emmons-beverly",
"escobar-chaparro-gloria",
"faculty-design",
"faculty-music",
"fader-oren",
"fagan-charling-c.",
"fajardo-margarita",
"farrell-christine",
"febos-melissa",
"ferguson-kim",
"ferraiolo-angela",
"ferrell-carolyn",
"folkman-marjorie",
"forbes-barbara",
"forte-joseph-c.",
"foulk-t.-griffith",
"frankel-marvin",
"frazier-melissa",
"frears-will",
"friedman-donald",
"fuchs-marek",
"gabaston-liza",
"garcia-laura",
"gardinier-suzanne",
"gay-jackson",
"germano-roy",
"gessen-keith",
"gillis-graeme",
"gilvary-sara",
"goldberg-myra",
"goldman-nina",
"goldray-martin",
"gorn-cindy",
"gould-peggy",
"greb-anne",
"greenberg-michael",
"greenwald-maggie",
"grewal-gwenda-lin",
"griepp-eva-bostein",
"griffiths-rachel-eliza",
"grob-rachel-n.",
"gross-susan",
"guest-artists-",
"guests-",
"gustafson-barret-charlie",
"hallberg-garth-risk",
"handy-catherine",
"hansen-anneke",
"hardy-dave",
"harris-hilda",
"harvey-matthea",
"hassan-sarah",
"hebron-mary",
"helias-mark",
"henkin-joshua",
"heppermann-ann",
"herb-alice",
"hercher-laura",
"heredia-luisa-laura",
"herships-sally",
"hersh-michelle",
"higgins-niko",
"hill-kathleen",
"hoch-james",
"hollander-david",
"hong-cathy-park",
"hoover-suzanne-r.",
"horowitz-james",
"howell-fanon",
"howe-marie",
"hsu-tishan",
"hultman-iréne",
"human-genetics-faculty-",
"humbaugh-erin",
"hurlin-dan",
"hwang-koosil-ja",
"hyman-yehuda",
"iacoboni-daniel",
"iliatova-vera",
"index",
"index.xml",
"ingliss-robert",
"jablonski-meghan",
"jacques-lmsw-christobal--j.",
"james-tara-elise",
"jansma-kristopher",
"jeter-james",
"johnson-daniel",
"johnson-kate-knapp",
"johnson-rebecca-o.",
"johnston-elizabeth",
"jones-alwin-a.-d.",
"jones-brian",
"kahler-jean",
"kaplan-shirley",
"kart-susan",
"katz-kuniko",
"kaufmann-kathy",
"kelley-william-melvin",
"kelly-deborah",
"kempson-sibyl",
"kerekes-paul",
"ketchum-barbara-bray",
"khakpour-porochista",
"kilroy-marac-kathleen",
"king-daniel",
"king-jonathan",
"kirsch-adam",
"klass-david",
"klinkenborg-verlyn",
"korsunskaia-ekaterina",
"kreider-timothy",
"kronn-david",
"krugman-jason",
"krupat-arnold",
"kurland-justine",
"kyle-peter",
"lachapelle-mary",
"lago-eduardo",
"landdeck-kevin",
"lang-allen",
"lauinger-ann",
"lauinger-joseph",
"lavigne-sharon",
"lawrence-karen-r.",
"lee-michelle",
"lee-tom",
"leonelli-laurie-beth",
"leveau-eric",
"lewis-linwood-j.",
"lewis-margot",
"lieber-caroline",
"lieberman-sandy",
"lieu-jocelyn",
"lin-tao",
"liu-wan-chun",
"liu-wen",
"long-laura-i.",
"lux-thomas",
"lyons-robert",
"machugh-doug",
"macías-patrisia",
"macmillan-brian",
"macpherson-greg",
"magnuson-robert",
"maillo-pozo-ruben",
"manago-alexander-merceditas",
"mañago-alexander-merceditas",
"mandel-thomas",
"mark-rona-naomi",
"marshall-james",
"martinez-deluca-margaret",
"matthews-amy",
"may-juliana",
"mazmanian-victor",
"mccarthy-janelle",
"mcdaniel-jeffrey",
"mcfarland-stephen",
"mcghee-elena",
"mcguire-liz",
"mcintyre-dianne",
"mcpherson-elizabeth",
"mcree-william-d.",
"means-angelia",
"medley-cassandra",
"meira-la",
"melnick-jodi-melnick",
"miller-jeffrey",
"miller-timothy",
"mills-joseph",
"mills-nicolaus",
"minsky-greta",
"misra-lavanya",
"mitchell-rashaun",
"mizelle-nike",
"mizrahi-terry",
"moe-ruth",
"mofidi-shideh",
"moger-angela",
"moos-katherine",
"morejon-diana-punales",
"morris-mary",
"mort-bari",
"morton-brian",
"mosolino-april-reynolds",
"moudud-jamee-k.",
"muchmore-patrick",
"muldavin-joshua",
"murolo-priscilla",
"murray-katie",
"muther-catherine",
"naka-cheiko",
"naka-chieko",
"neely-evan",
"negroni-maria",
"neskar-ellen",
"neumann-david",
"newhouse-erica",
"newman-leigh",
"nolin-sally",
"novas-julie",
"nurkse-dennis",
"oconnor-john",
"oconnor-stephen",
"offill-jenny",
"olson-leah",
"ording-philip",
"orlandersmith-dael",
"ornstein-sloan-magdalena",
"oshea-marygrace",
"oyama-sayuri-i.",
"oziashvili-yekaterina",
"paramaditha-intan",
"parker-matthew",
"partin-ted",
"pearce-nicole",
"peixoto-michael",
"pelletier-carol-ann",
"peritz-david",
"petty-lauren",
"pfordresher-jeanne1",
"philipps-kris",
"philogene-gina",
"pierce-young-eddye",
"pilkington-kevin",
"pollack-maika",
"pollak-max",
"porter-karen",
"porter-mary-a.",
"power-marilyn",
"prieto-josé-manuel",
"rainer-yvonne",
"raja-kanishka",
"rakoff-joanna-smith",
"redel-victoria",
"reich-elsa",
"reifler-nelly",
"reilly-janet",
"reksten-nicholas",
"rezai-hamid",
"rhodes-martha",
"richards-peter",
"risher-elise",
"rivera-colon-edgar",
"robinson-sandra",
"rodenbeck-judith",
"rodgers-liz",
"romano-patrick",
"rorandelli-tristana",
"rosenthal-lucy",
"rouse-shahnaz",
"rudner-sara",
"ruen-kathleen",
"ryan-david",
"sampson-efeya-ifadayo-m",
"sanborn-erica",
"sanchez-misael",
"sanders-wayne",
"sands-kristin-zahra",
"saptanyana-nyoman",
"saxon-la-rose",
"schachter-amanda",
"schecter-barbara",
"scheier-fanchon-miller",
"schlesinger-mark-j.",
"schmidt-carsten",
"schneider-ursula",
"schorsch-jonathan",
"schrader-astrid",
"schultz-anthony",
"schultz-tony",
"scotch-marmo-malia",
"sealander-rebecca",
"seibel-jean",
"seigle-samuel-b.",
"serafini-sauli-judith-p.",
"seshadri-vijay",
"shafer-susan-h.",
"shemy-deganit",
"sherman-maxine",
"shuford-gabriel",
"shullenberger-william",
"shulman-mark-r.",
"siff-michael",
"silber-joan",
"simons-lake",
"singh-kanwal",
"singh-paul",
"sivesind-david",
"sizer-lyde-cullen",
"slichter-jacob",
"smith-michael-j.",
"smoler-fredric",
"snead-pamela",
"sohn-sungrai",
"soiseth-alexandra",
"sosnowy-collette",
"southgate-martha",
"spano-michael",
"speer-james-w.",
"speight-rico",
"spencer-stuart",
"starbuck-robin",
"sternfeld-joel",
"stevens-brooke",
"strype-frederick-michael",
"swann-sterling",
"swoboda-philip",
"tba-faculty",
"technical-staff-",
"thomas-nadeen-m.",
"thom-rose-anne",
"thurber-lucy",
"to-be-announced-",
"tomasulo-frank",
"truax-alice",
"turvey-malcolm",
"ulmert-megan",
"uttley-lois",
"vahrenwald-michael",
"vasudevan-preeti",
"vesely-flad-rima",
"vincenot-dash-stella",
"vitkin-marina",
"voice-faculty-",
"volpe-francine",
"wachs-ilja",
"weil-laura",
"weis-cathy",
"weiss-heidi",
"wentworth-jean",
"weschler-lawrence",
"westwater-kathy",
"wiersma-cal",
"wilbur-jennifer-scalia",
"wilcox-sarah",
"wilford-sara",
"wilson-fiona",
"wilson-matthew",
"winter-joe",
"winters-heather",
"woodard-komozi",
"woolfson-joseph-w.",
"wright-alexandra",
"wunderlich-mark",
"yamamoto-miyabi",
"yang-min",
"yannelli-john",
"yates-jonathan",
"yin-mali",
"yionoulis-evan",
"yoo-mia",
"yoon-paul",
"young-thomas",
"zambreno-kate",
"zerfas-francine",
"zerner-charles",
"zevin-dan",
"zollar-keisha",
"zoref-carol",
"zuern-elke"
  ]


  // $('body').append('<iframe name="result" id="result" style="position:fixed; bottom:0; right:0; width:50%; background:#fff; transition:height .35s;"></iframe><div class="btn btn-info" id="iframe-expander" style="position:fixed; bottom:0; right:50%;">Expand Results</div>');
  // $('#iframe-expander').click(function() { $('#result').toggleClass('bigger'); });

  $('.event-form .collapser').click(function() {\$(this).closest('.event-form').find('.event-details').slideToggle().parent().find('.btn-group').slideToggle()});

  String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
  }

  function selectizeOptions(arr) {
    options = [];
    $.each(arr, function(i,v) {
      options.push({text: v, value: v});
    });
    return options;
  }
  function selectItems(t) {
    items = [];
    if (t === 'channels') {items.push('WWW')}
    return items;
  }

  $('.event-form').each(function(i,v) {
    var id = $(this).data('id');
    window['selectize'+id] = [];
    $.each(types, function(i,t) {
      var inputID = 'sel-'+t+'-'+id;
      $(v).find('.event-tags').append('<div class="tag-section"><label for="'+inputID+ '">'+ t.capitalize() +'</label><input id="'+inputID+ '" name="'+ t +'" type="text"></div>');
      window['selectize'+id][i] = $('#'+inputID).selectize({
        delimiter: ";",
        options: selectizeOptions(window[t]),
        placeholder: t.capitalize(),
        items: selectItems(t),
        maxItems: t === "faculty" ? 1 : null
      });
    });

    $('#deleted_events').clone().removeAttr('id').wrap('<div class="tag-section deleted-events"/>').parent().appendTo( $(this).find('.event-details') ); 
    $(this).find('.deleted-events').append('<input type="submit" name="submit" class="btn btn-info btn-copy btn-action" value="Copy data"/>'); 
    $(this).find('.deleted-events select').selectize();

    $(this).find('.loading-tagging').remove();

    $('.event-actions').removeClass('hidden');
  });
  $('#deleted_events').remove();

  $('.event-form .btn-enable').click(function(e) {
    if ( $(this).is('.disabled') ) { e.preventDefault(); return false; }
    $(this).closest('form').attr('action','enable.php?year=$events_year');
    $(this).closest('form').find('h5').append('<div class="label label-success">Enable request sent</div>');
    $(this).closest('form').find('.btn-action').addClass('disabled');
    $(this).closest('form').find('.event-actions, .deleted-events').hide();
    id = $(this).closest('form').data('id');
    selectizedFormInputs = window['selectize'+id];
    if (typeof selectizedFormInputs !== 'undefined' && Array.isArray(selectizedFormInputs) ) {
      $.each(selectizedFormInputs, function() {
        this[0].selectize.lock();
      });
    }
  });
  $('.event-form .btn-reject').click(function(e) {
    if ( $(this).is('.disabled') ) { e.preventDefault(); return false; }
    $(this).closest('form').attr('action','reject.php?year=$events_year');
    $(this).closest('form').find('h5').append('<div class="label label-important">Reject request sent</div>');
    $(this).closest('form').find('.btn-action').addClass('disabled');
    $(this).closest('form').find('.event-actions, .event-tags, .deleted-events').hide();
  });
  $('.event-form .btn-copy').click(function(e) {
    if ( $(this).is('.disabled') ) { e.preventDefault(); return false; }
    $(this).closest('form').attr('action','copyData.php?year=$events_year');
    $(this).closest('form').find('h5').append('<div class="label label-info">Copy request sent</div>');
    $(this).closest('form').find('.btn-action').addClass('disabled');
    $(this).closest('form').find('.event-actions, .event-tags').hide();
  });

});

EOS;

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  global $acad_year;
  $pattern = '/^events\/'.$acad_year.'\/_pending\/'.$_GET['date'].'/';
  if (preg_match($pattern, $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/^_[a-z]/', $child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/[a-z]/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false.
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  // if ($asset["metadata"]->teaser != 'test') {
  //    $changed = true;
  //    $asset["metadata"]->teaser = 'test';
  // }
}

if (!$cron) {include('../html_header.php');}

if (array_key_exists('submit',$_POST)) {
  echo '<select name="deleted_event" id="deleted_events" class="deleted_events"> <option value="">Select a deleted event</option>';
  $deleted_folders = [$deleted_folder, $previous_deleted_folder];
  foreach ($deleted_folders as $key => $folder) {
    if ( isset($folder) ) {
      $del_folder = $client->read ( array ('authentication' => $auth, 'identifier' => array ('type' => 'folder', id => $folder ) ) );
      if ($del_folder->readReturn->success == 'true') {
        $del_asset = ( array ) $del_folder->readReturn->asset->folder;
        $deleted_events = $del_asset["children"]->child;
        foreach ($deleted_events as $del_event) {
          echo "<option value='".$del_event->id."'>".$del_event->path->path."</option>";
        }
      } else {
        if (!$cron) {echo "<option>Couldn't read deleted events folder.</option>";}
      }
    }
  }
  echo '</select>';
}



function readFolder($client, $auth, $id) {
  global $asset_type, $asset_children_type, $data, $o, $cron;
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {

    $asset = ( array ) $folder->readReturn->asset->$asset_type;
    if ($cron) {
      $o[4] .= "<h4>Folder: ".$asset["path"]."</h4>";
    } elseif ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    if ($_POST['children'] == 'on' && !$cron) {
      echo '<button class="btn" href="#cModal'.$asset['id'].'" data-toggle="modal">View Children</button><div id="cModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
        print_r($asset["children"]); // Shows all the children of the folder
      echo '</div></div>';
    }
    indexFolder($client, $auth, $asset);
  } else {
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read folder: '.$asset["path"].'</div>';
    } else {
      echo '<div class="f">Failed to read folder: '.$asset["path"].'</div>';
    }
  }
}
function indexFolder($client, $auth, $asset) {
  global $asset_type, $asset_children_type, $data, $o, $cron;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  foreach($asset["children"]->child as $child) {
    if ($child->type == strtolower($asset_children_type)) {
      if (pagetest($child))
        readPage($client, $auth, array ('type' => $child->type, 'id' => $child->id), $child->type);
    } elseif ($child->type === strtolower($asset_type)) {
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => $child->type, 'id' => $child->id));
    }
  }
}

function readPage($client, $auth, $id, $type) {
  global $asset_type, $asset_children_type, $data, $o, $cron;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    // For some reason the names of asset differ from the returned object
    $returned_type = '';
    foreach ($reply->readReturn->asset as $t => $a) {
      if (!empty($a)) {$returned_type = $t;}
    }

    $asset = ( array ) $reply->readReturn->asset->$returned_type;
    if ($cron) {
      $o[3] .= '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></h4>";
    } elseif ($_POST['asset'] == 'on') {
      $name = '';
      if (!$asset['path']) {$name = $asset['name'];}

      foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
        if ($dyn->name == 'begin') {
          $begin = intval($dyn->fieldValues->fieldValue->value)/1000;
        }
        if ($dyn->name == 'end') {
          $end = intval($dyn->fieldValues->fieldValue->value)/1000;
        }
        if ($dyn->name == 'sponsor') {
          $sponsor = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'location') {
          $location = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'type') {
          $type = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'recurring') {
          $recurring = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'eventsource') {
          $eventsource = $dyn->fieldValues->fieldValue->value;
        }
      }
      foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdn) {
        if ($sdn->identifier == 'main_column') {
          foreach ($sdn->structuredDataNodes->structuredDataNode as $main_node) {
            if ($main_node->identifier == 'content') {
              $content = $main_node->text;
            }
          }
        }
      }
      if (strpos($_POST['client'], 'https://cms.slc.edu:8443') === false) {
        $cmsLink = 'https://cms.slc.edu:7443';
      } else {
        $cmsLink = 'https://cms.slc.edu:8443';
      }
      echo '<form class="event-form clearfix" method="POST" target="result-'.$asset['id'].'" data-id="'.$asset['id'].'">';
        echo '<div class="btn btn-info pull-right collapser">Expand/Collapse</div>';
        echo '<input type="hidden" name="id" value="'.$asset['id'].'"/>';
        echo '<h4><a target="_blank" href="'. $cmsLink .'/entity/open.act?id='.$asset['id'].'&type=page&">'.$asset['metadata']->title.'</a></h4>';
        echo '<div class="event-details">';
          echo '<div>'.$asset['path'].$name.'</div>';
          echo '<div class="k">'.date("D M dS, H:i", $begin).'</div> - <div class="k">'.date('D M dS, H:i', $end).'</div>'.($recurring == 'False' ? '' : '<div class="label label-info">Recurring</div>').' <a class="label label-success" target="_blank" href="'.$eventsource.'">Source</a>';
          echo '<div><strong>Location:</strong> '.$location.'</div>';
          echo '<div><strong>Sponsor:</strong> '.$sponsor.'</div>';
          echo '<div><strong>Type:</strong> '.$type.'</div>';
          echo '<div style="max-width:600px; background:#dde;">'.$asset['metadata']->summary.'</div>';
          echo '<div style="max-width:600px;">'.$content.'</div>';
          echo '<div class="event-tags"><div class="loading-tagging">Loading...</div></div>';
          echo '<input name="login" type="hidden" value="'. $_POST['login'] .'"/><input name="password" type="hidden" value="'. $_POST['password'] .'"/><input name="client" type="hidden" value="'. $_POST['client'] .'"/><input name="type" type="hidden" value="page"/><input name="action" type="hidden" value="edit"/>';
          echo '<div class="event-actions hidden text-center"><div class="btn-group"><input type="submit" name="submit" class="btn btn-success btn-enable btn-action" value="Enable and Tag '. $asset['metadata']->title .'"/><input type="submit" name="submit" class="btn btn-warning btn-reject btn-action" value="Reject"/></div> OR...</div>';
        echo '</div>';
        echo '<h5>Action results:</h5>';
        echo '<iframe name="result-'. $asset['id'] .'" id="result-'. $asset['id'] .'" class="actionOutputIframe"></iframe>';
      echo "</form>";
    }

    if (edittest($asset)) {
      if (!$cron) {echo '<div class="page">';}
      if ($_POST['before'] == 'on' && !$cron) {
        echo '<button class="btn" href="#bModal'.$asset['id'].'" data-toggle="modal">View Before</button><div id="bModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
          print_r($asset); // Shows the page in all its glory
        echo '</div></div>';
      }

      if (!$cron) {
        echo "<script type='text/javascript'>var page_".$asset['id']." = ";
        print_r(json_encode($asset));
        echo '; console.log(page_'.$asset['id'].')';
        echo "</script>";
      }

      if (!$cron) {echo '</div>';}
    }

  } else {
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read page: '.$id.'</div>';
    } else {
      echo '<div class="f">Failed to read page: '.$id.'</div>';
    }
  }
}

?>
