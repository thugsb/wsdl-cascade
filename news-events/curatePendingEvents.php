<?php
date_default_timezone_set('America/New_York');
$title = 'Curate Events';

include_once('eventFolderIDs.php');

// $type_override = 'page';
$start_asset = $pending_folder;

$message = 'NOTE: This page requires JavaScript. Optionally, use the GET argument "?date=yyyy-mm-dd" to filter by date, where dd and mm are not required.';
$user = $_POST['login'];
$password = $_POST['password'];
$client = $_POST['client'];
$script = <<<EOS

$(function() {
  $('body').append('<iframe name="result" id="result" style="position:fixed; bottom:0; right:0; width:50%; background:#fff; transition:height .35s;"></iframe><div class="btn btn-info" id="iframe-expander" style="position:fixed; bottom:0; right:50%;">Expand Results</div>');
  $('#iframe-expander').click(function() { $('#result').toggleClass('bigger'); });
  
  $('.event-form').prepend('<div class="btn btn-info pull-right collapser">Expand/Collapse</div>');
  $('.event-form .collapser').click(function() {\$(this).parent().find('.tag-section').slideToggle().parent().find('.btn-group').slideToggle()});
  
  $('.event-form').append('<div class="tag-section tag-areas"><h3>Academics</h3><label><input type="checkbox" name="areas[]" value="Humanities"/>Humanities</label><label><input type="checkbox" name="areas[]" value="History and the Social Sciences"/>History and the Social Sciences</label><label><input type="checkbox" name="areas[]" value="Science and Mathematics"/>Science and Mathematics</label><label><input type="checkbox" name="areas[]" value="Creative and Performing Arts"/>Creative and Performing Arts</label></div>');
  $('.event-form').append('<div class="tag-section tag-disciplines"><label><input type="checkbox" name="disciplines[]" value="Africana Studies"/>Africana Studies</label><label><input type="checkbox" name="disciplines[]" value="Anthropology"/>Anthropology</label><label><input type="checkbox" name="disciplines[]" value="Art History"/>Art History</label><label><input type="checkbox" name="disciplines[]" value="Asian Studies"/>Asian Studies</label><label><input type="checkbox" name="disciplines[]" value="Biology"/>Biology</label><label><input type="checkbox" name="disciplines[]" value="Chemistry"/>Chemistry</label><label><input type="checkbox" name="disciplines[]" value="Chinese"/>Chinese</label><label><input type="checkbox" name="disciplines[]" value="Classics"/>Classics</label><label><input type="checkbox" name="disciplines[]" value="Cognitive and Brain Science"/>Cognitive and Brain Science</label><label><input type="checkbox" name="disciplines[]" value="Computer Science"/>Computer Science</label><label><input type="checkbox" name="disciplines[]" value="Dance"/>Dance</label><label><input type="checkbox" name="disciplines[]" value="Design Studies"/>Design Studies</label><label><input type="checkbox" name="disciplines[]" value="Development Studies"/>Development Studies</label><label><input type="checkbox" name="disciplines[]" value="Economics"/>Economics</label><label><input type="checkbox" name="disciplines[]" value="Environmental Studies"/>Environmental Studies</label><label><input type="checkbox" name="disciplines[]" value="Ethnic and Diasporic Studies"/>Ethnic and Diasporic Studies</label><label><input type="checkbox" name="disciplines[]" value="Film History"/>Film History</label><label><input type="checkbox" name="disciplines[]" value="French"/>French</label><label><input type="checkbox" name="disciplines[]" value="Games, Interactivity, and Playable Media"/>Games, Interactivity, and Playable Media</label><label><input type="checkbox" name="disciplines[]" value="Gender and Sexuality Studies"/>Gender and Sexuality Studies</label><label><input type="checkbox" name="disciplines[]" value="Geography"/>Geography</label><label><input type="checkbox" name="disciplines[]" value="German"/>German</label><label><input type="checkbox" name="disciplines[]" value="Greek"/>Greek</label><label><input type="checkbox" name="disciplines[]" value="Health, Science, and Society"/>Health, Science, and Society</label><label><input type="checkbox" name="disciplines[]" value="History"/>History</label><label><input type="checkbox" name="disciplines[]" value="International Studies"/>International Studies</label><label><input type="checkbox" name="disciplines[]" value="Italian"/>Italian</label><label><input type="checkbox" name="disciplines[]" value="Japanese"/>Japanese</label><label><input type="checkbox" name="disciplines[]" value="Latin"/>Latin</label><label><input type="checkbox" name="disciplines[]" value="Latin American and Latino/a Studies"/>Latin American and Latino/a Studies</label><label><input type="checkbox" name="disciplines[]" value="LGBT"/>LGBT</label><label><input type="checkbox" name="disciplines[]" value="Literature"/>Literature</label><label><input type="checkbox" name="disciplines[]" value="Mathematics"/>Mathematics</label><label><input type="checkbox" name="disciplines[]" value="Middle Eastern and Islamic Studies"/>Middle Eastern and Islamic Studies</label><label><input type="checkbox" name="disciplines[]" value="Modern Languages and Literatures"/>Modern Languages and Literatures</label><label><input type="checkbox" name="disciplines[]" value="Music"/>Music</label><label><input type="checkbox" name="disciplines[]" value="Philosophy"/>Philosophy</label><label><input type="checkbox" name="disciplines[]" value="Physics"/>Physics</label><label><input type="checkbox" name="disciplines[]" value="Political Economy"/>Political Economy</label><label><input type="checkbox" name="disciplines[]" value="Politics"/>Politics</label><label><input type="checkbox" name="disciplines[]" value="Pre-med"/>Pre-med</label><label><input type="checkbox" name="disciplines[]" value="Psychology"/>Psychology</label><label><input type="checkbox" name="disciplines[]" value="Public Policy"/>Public Policy</label><label><input type="checkbox" name="disciplines[]" value="Religion"/>Religion</label><label><input type="checkbox" name="disciplines[]" value="Russian"/>Russian</label><label><input type="checkbox" name="disciplines[]" value="Science, Technology, and Society"/>Science, Technology, and Society</label><label><input type="checkbox" name="disciplines[]" value="Sociology"/>Sociology</label><label><input type="checkbox" name="disciplines[]" value="Spanish"/>Spanish</label><label><input type="checkbox" name="disciplines[]" value="Theatre"/>Theatre</label><label><input type="checkbox" name="disciplines[]" value="Urban Studies"/>Urban Studies</label><label><input type="checkbox" name="disciplines[]" value="Visual Arts"/>Visual Arts</label><label><input type="checkbox" name="disciplines[]" value="Digital Imagery"/>Digital Imagery</label><label><input type="checkbox" name="disciplines[]" value="Drawing"/>Drawing</label><label><input type="checkbox" name="disciplines[]" value="Filmmaking"/>Filmmaking</label><label><input type="checkbox" name="disciplines[]" value="Painting"/>Painting</label><label><input type="checkbox" name="disciplines[]" value="Photography"/>Photography</label><label><input type="checkbox" name="disciplines[]" value="Printmaking"/>Printmaking</label><label><input type="checkbox" name="disciplines[]" value="Sculpture"/>Sculpture</label><label><input type="checkbox" name="disciplines[]" value="Women’s Studies"/>Women’s Studies</label><label><input type="checkbox" name="disciplines[]" value="Writing"/>Writing</label></div>');
  $('.event-form').append('<div class="tag-section tag-programs"><label><input type="checkbox" name="programs[]" value="Art of Teaching"/>Art of Teaching</label><label><input type="checkbox" name="programs[]" value="Child Development"/>Child Development</label><label><input type="checkbox" name="programs[]" value="MFA Dance"/>MFA Dance</label><label><input type="checkbox" name="programs[]" value="Dance Movement Therapy"/>Dance Movement Therapy</label><label><input type="checkbox" name="programs[]" value="Health Advocacy"/>Health Advocacy</label><label><input type="checkbox" name="programs[]" value="Human Genetics"/>Human Genetics</label><label><input type="checkbox" name="programs[]" value="MFA Theatre"/>MFA Theatre</label><label><input type="checkbox" name="programs[]" value="Womens History"/>Womens History</label><label><input type="checkbox" name="programs[]" value="MFA Writing"/>MFA Writing</label></div>');
  $('.event-form').append('<div class="tag-section tag-studies"><label><input type="checkbox" name="studies[]" value="Continuing Education"/>Continuing Education</label><label><input type="checkbox" name="studies[]" value="Child Development Institute"/>Child Development Institute</label><label><input type="checkbox" name="studies[]" value="Early Childhood Center"/>Early Childhood Center</label><label><input type="checkbox" name="studies[]" value="Special Programs"/>Special Programs</label><label><input type="checkbox" name="studies[]" value="Study Abroad"/>Study Abroad</label><label><input type="checkbox" name="studies[]" value="Writing Institute"/>Writing Institute</label></div>');
  $('.event-form').append('<div class="tag-section tag-audiences"><h3>Audiences</h3><label><input type="checkbox" name="audiences[]" value="Administration"/>Administration</label><label><input type="checkbox" name="audiences[]" value="Alumni"/>Alumni</label><label><input type="checkbox" name="audiences[]" value="Donors"/>Donors</label><label><input type="checkbox" name="audiences[]" value="Faculty"/>Faculty</label><label><input type="checkbox" name="audiences[]" value="Graduate"/>Graduate</label><label><input type="checkbox" name="audiences[]" value="High School"/>High School</label><label><input type="checkbox" name="audiences[]" value="Neighbors"/>Neighbors</label><label><input type="checkbox" name="audiences[]" value="Parents"/>Parents</label><label><input type="checkbox" name="audiences[]" value="President"/>President</label><label><input type="checkbox" name="audiences[]" value="Public"/>Public</label><label><input type="checkbox" name="audiences[]" value="Staff"/>Staff</label><label><input type="checkbox" name="audiences[]" value="Students"/>Students</label><label><input type="checkbox" name="audiences[]" value="Undergraduate"/>Undergraduate</label></div>');
  $('.event-form').append('<div class="tag-section tag-themes"><h3>Themes</h3><label><input type="checkbox" name="themes[]" value="Academics"/>Academics</label><label><input type="checkbox" name="themes[]" value="Advancement"/>Advancement</label><label><input type="checkbox" name="themes[]" value="Alumni"/>Alumni</label><label><input type="checkbox" name="themes[]" value="Athletics"/>Athletics</label><label><input type="checkbox" name="themes[]" value="Careers"/>Careers</label><label><input type="checkbox" name="themes[]" value="Commencement"/>Commencement</label><label><input type="checkbox" name="themes[]" value="Graduate Admission"/>Graduate Admission</label><label><input type="checkbox" name="themes[]" value="NYC and Local"/>NYC and Local</label><label><input type="checkbox" name="themes[]" value="Reunion"/>Reunion</label><label><input type="checkbox" name="themes[]" value="Student Life"/>Student Life</label><label><input type="checkbox" name="themes[]" value="Undergraduate Admission"/>Undergraduate Admission</label></div>');
  $('.event-form').append('<div class="tag-section tag-sponsors"><h3>Sponsors</h3><label><input type="checkbox" name="sponsors[]" value="Alumni Relations"/>Alumni Relations</label><label><input type="checkbox" name="sponsors[]" value="Career Services"/>Career Services</label><label><input type="checkbox" name="sponsors[]" value="DAPS"/>DAPS</label><label><input type="checkbox" name="sponsors[]" value="Real Talk"/>Real Talk</label><label><input type="checkbox" name="sponsors[]" value="SLAC"/>SLAC</label></div>');
  
  $('.event-form').append('<div class="tag-section tag-faculty">Faculty: <select name="faculty-tag" id="faculty-selector"><option value="" selected="selected"></option><option value="abernethy-colin-d.">abernethy-colin-d.</option><option value="abraham-julie">abraham-julie</option><option value="abrams-samuel">abrams-samuel</option><option value="abuba-ernest-h.">abuba-ernest-h.</option><option value="acting-faculty-">acting-faculty-</option><option value="adams-jefferson">adams-jefferson</option><option value="afzal-cameron-c.">afzal-cameron-c.</option><option value="agard-jones-vanessa">agard-jones-vanessa</option><option value="aggarwal-ujju">aggarwal-ujju</option><option value="agresta-kirsten">agresta-kirsten</option><option value="alan-gilbert">alan-gilbert</option><option value="albarelli-gerry">albarelli-gerry</option><option value="alexander-glenn">alexander-glenn</option><option value="alexis-melissa">alexis-melissa</option><option value="algire-andrew">algire-andrew</option><option value="ancona-marina">ancona-marina</option><option value="anderson-abraham">anderson-abraham</option><option value="anderson-william">anderson-william</option><option value="andriole-stephanie">andriole-stephanie</option><option value="anhalt-emily-katz">anhalt-emily-katz</option><option value="appel-cathy">appel-cathy</option><option value="arditi-neil">arditi-neil</option><option value="ash-erin">ash-erin</option><option value="bajaj-komal">bajaj-komal</option><option value="baker-damani">baker-damani</option><option value="baker-nancy">baker-nancy</option><option value="balasubramaniam-tanjore">balasubramaniam-tanjore</option><option value="balkite-elisabeth-a.">balkite-elisabeth-a.</option><option value="barenboim-carl">barenboim-carl</option><option value="barenboim-deanna">barenboim-deanna</option><option value="barnard-mary">barnard-mary</option><option value="basilio-jorge">basilio-jorge</option><option value="beard-jo-ann">beard-jo-ann</option><option value="beavan-colin">beavan-colin</option><option value="begelman-igor">begelman-igor</option><option value="belcastro-sarah-marie">belcastro-sarah-marie</option><option value="benzoni-stefania">benzoni-stefania</option><option value="berg-bruce">berg-bruce</option><option value="bernstein-david">bernstein-david</option><option value="biscardi-chester">biscardi-chester</option><option value="blake-lorna-knowles">blake-lorna-knowles</option><option value="blalock-lucas">blalock-lucas</option><option value="bosselaar-laure-anne">bosselaar-laure-anne</option><option value="bower-bill-l.">bower-bill-l.</option><option value="bradshaw-patti">bradshaw-patti</option><option value="brand-roy">brand-roy</option><option value="breitbart-vicki">breitbart-vicki</option><option value="brodzki-bella">brodzki-bella</option><option value="brown-adam">brown-adam</option><option value="brown-karen">brown-karen</option><option value="brown-kurt">brown-kurt</option><option value="brown-wesley">brown-wesley</option><option value="buckley-jennifer">buckley-jennifer</option><option value="bukiet-melvin-jules">bukiet-melvin-jules</option><option value="burnley-gary">burnley-gary</option><option value="calvin-scott">calvin-scott</option><option value="canick-jacob">canick-jacob</option><option value="carbon-lorayne">carbon-lorayne</option><option value="carstens-marie">carstens-marie</option><option value="carter-suzanne">carter-suzanne</option><option value="castillo-mauricio">castillo-mauricio</option><option value="castriota-david">castriota-david</option><option value="catanzaro-william">catanzaro-william</option><option value="catterson-pat">catterson-pat</option><option value="chang-tina">chang-tina</option><option value="chapman-susannah">chapman-susannah</option><option value="charles-persis">charles-persis</option><option value="cheng-eileen-ka-may">cheng-eileen-ka-may</option><option value="chen-mengyu">chen-mengyu</option><option value="chen-priscilla">chen-priscilla</option><option value="cho-john-song-pae">cho-john-song-pae</option><option value="christle-heather">christle-heather</option><option value="chung-una">chung-una</option><option value="clark-sarah">clark-sarah</option><option value="cleary-heather">cleary-heather</option><option value="cohen-rachel">cohen-rachel</option><option value="confoy-kevin">confoy-kevin</option><option value="conlan-kelli">conlan-kelli</option><option value="corva-dominic">corva-dominic</option><option value="cottrell-margaret">cottrell-margaret</option><option value="cottrell-peggy">cottrell-peggy</option><option value="cramer-michael">cramer-michael</option><option value="cressman-drew-e.">cressman-drew-e.</option><option value="cruz-cynthia">cruz-cynthia</option><option value="curulla-annelle">curulla-annelle</option><option value="dance-faculty-">dance-faculty-</option><option value="dasgupta-sayantani">dasgupta-sayantani</option><option value="datta-rhea">datta-rhea</option><option value="davis-michael">davis-michael</option><option value="de-leon-cora">de-leon-cora</option><option value="delne-claudy">delne-claudy</option><option value="de-sena-isabel">de-sena-isabel</option><option value="desjarlais-robert-r.">desjarlais-robert-r.</option><option value="devine-emily">devine-emily</option><option value="diamond-david">diamond-david</option><option value="dillard-mary">dillard-mary</option><option value="ditkoff-beth-ann">ditkoff-beth-ann</option><option value="dixon-jonathan">dixon-jonathan</option><option value="dizenko-natalia">dizenko-natalia</option><option value="dobyns-stephen">dobyns-stephen</option><option value="dodds-jerrilynn">dodds-jerrilynn</option><option value="dolan-siobhan">dolan-siobhan</option><option value="dollinger-roland">dollinger-roland</option><option value="donzelli-aurora">donzelli-aurora</option><option value="downs-patrick">downs-patrick</option><option value="doyle-charlotte-l.">doyle-charlotte-l.</option><option value="driscoll-kermit">driscoll-kermit</option><option value="drucker-jan">drucker-jan</option><option value="du-boff-jill">du-boff-jill</option><option value="duce-scott">duce-scott</option><option value="dufresne-angela">dufresne-angela</option><option value="duggan-niamh">duggan-niamh</option><option value="dumbutshena-rujeko">dumbutshena-rujeko</option><option value="duran-nancy">duran-nancy</option><option value="durcan-judith">durcan-judith</option><option value="dynner-glenn">dynner-glenn</option><option value="earle-jason">earle-jason</option><option value="early-michael">early-michael</option><option value="eger-david-j.">eger-david-j.</option><option value="ekman-june">ekman-june</option><option value="ellicson-kirsten">ellicson-kirsten</option><option value="ellis-matthew">ellis-matthew</option><option value="ellis-thomas-sayers">ellis-thomas-sayers</option><option value="emmons-beverly">emmons-beverly</option><option value="escobar-chaparro-gloria">escobar-chaparro-gloria</option><option value="faculty-design">faculty-design</option><option value="faculty-music">faculty-music</option><option value="fader-oren">fader-oren</option><option value="fagan-charling-c.">fagan-charling-c.</option><option value="fajardo-margarita">fajardo-margarita</option><option value="farrell-christine">farrell-christine</option><option value="febos-melissa">febos-melissa</option><option value="ferguson-kim">ferguson-kim</option><option value="ferraiolo-angela">ferraiolo-angela</option><option value="ferrell-carolyn">ferrell-carolyn</option><option value="folkman-marjorie">folkman-marjorie</option><option value="forbes-barbara">forbes-barbara</option><option value="forte-joseph-c.">forte-joseph-c.</option><option value="foulk-t.-griffith">foulk-t.-griffith</option><option value="frankel-marvin">frankel-marvin</option><option value="frazier-melissa">frazier-melissa</option><option value="frears-will">frears-will</option><option value="friedman-donald">friedman-donald</option><option value="fuchs-marek">fuchs-marek</option><option value="gabaston-liza">gabaston-liza</option><option value="garcia-laura">garcia-laura</option><option value="gardinier-suzanne">gardinier-suzanne</option><option value="gay-jackson">gay-jackson</option><option value="germano-roy">germano-roy</option><option value="gessen-keith">gessen-keith</option><option value="gillis-graeme">gillis-graeme</option><option value="gilvary-sara">gilvary-sara</option><option value="goldberg-myra">goldberg-myra</option><option value="goldman-nina">goldman-nina</option><option value="goldray-martin">goldray-martin</option><option value="gorn-cindy">gorn-cindy</option><option value="gould-peggy">gould-peggy</option><option value="greb-anne">greb-anne</option><option value="greenberg-michael">greenberg-michael</option><option value="greenwald-maggie">greenwald-maggie</option><option value="grewal-gwenda-lin">grewal-gwenda-lin</option><option value="griepp-eva-bostein">griepp-eva-bostein</option><option value="griffiths-rachel-eliza">griffiths-rachel-eliza</option><option value="grob-rachel-n.">grob-rachel-n.</option><option value="gross-susan">gross-susan</option><option value="guest-artists-">guest-artists-</option><option value="guests-">guests-</option><option value="gustafson-barret-charlie">gustafson-barret-charlie</option><option value="hallberg-garth-risk">hallberg-garth-risk</option><option value="handy-catherine">handy-catherine</option><option value="hansen-anneke">hansen-anneke</option><option value="hardy-dave">hardy-dave</option><option value="harris-hilda">harris-hilda</option><option value="harvey-matthea">harvey-matthea</option><option value="hassan-sarah">hassan-sarah</option><option value="hebron-mary">hebron-mary</option><option value="helias-mark">helias-mark</option><option value="henkin-joshua">henkin-joshua</option><option value="heppermann-ann">heppermann-ann</option><option value="herb-alice">herb-alice</option><option value="hercher-laura">hercher-laura</option><option value="heredia-luisa-laura">heredia-luisa-laura</option><option value="herships-sally">herships-sally</option><option value="hersh-michelle">hersh-michelle</option><option value="higgins-niko">higgins-niko</option><option value="hill-kathleen">hill-kathleen</option><option value="hoch-james">hoch-james</option><option value="hollander-david">hollander-david</option><option value="hong-cathy-park">hong-cathy-park</option><option value="hoover-suzanne-r.">hoover-suzanne-r.</option><option value="horowitz-james">horowitz-james</option><option value="howell-fanon">howell-fanon</option><option value="howe-marie">howe-marie</option><option value="hsu-tishan">hsu-tishan</option><option value="hultman-iréne">hultman-iréne</option><option value="human-genetics-faculty-">human-genetics-faculty-</option><option value="humbaugh-erin">humbaugh-erin</option><option value="hurlin-dan">hurlin-dan</option><option value="hwang-koosil-ja">hwang-koosil-ja</option><option value="hyman-yehuda">hyman-yehuda</option><option value="iacoboni-daniel">iacoboni-daniel</option><option value="iliatova-vera">iliatova-vera</option><option value="index">index</option><option value="index.xml">index.xml</option><option value="ingliss-robert">ingliss-robert</option><option value="jablonski-meghan">jablonski-meghan</option><option value="jacques-lmsw-christobal--j.">jacques-lmsw-christobal--j.</option><option value="james-tara-elise">james-tara-elise</option><option value="jansma-kristopher">jansma-kristopher</option><option value="jeter-james">jeter-james</option><option value="johnson-daniel">johnson-daniel</option><option value="johnson-kate-knapp">johnson-kate-knapp</option><option value="johnson-rebecca-o.">johnson-rebecca-o.</option><option value="johnston-elizabeth">johnston-elizabeth</option><option value="jones-alwin-a.-d.">jones-alwin-a.-d.</option><option value="jones-brian">jones-brian</option><option value="kahler-jean">kahler-jean</option><option value="kaplan-shirley">kaplan-shirley</option><option value="kart-susan">kart-susan</option><option value="katz-kuniko">katz-kuniko</option><option value="kaufmann-kathy">kaufmann-kathy</option><option value="kelley-william-melvin">kelley-william-melvin</option><option value="kelly-deborah">kelly-deborah</option><option value="kempson-sibyl">kempson-sibyl</option><option value="kerekes-paul">kerekes-paul</option><option value="ketchum-barbara-bray">ketchum-barbara-bray</option><option value="khakpour-porochista">khakpour-porochista</option><option value="kilroy-marac-kathleen">kilroy-marac-kathleen</option><option value="king-daniel">king-daniel</option><option value="king-jonathan">king-jonathan</option><option value="kirsch-adam">kirsch-adam</option><option value="klass-david">klass-david</option><option value="klinkenborg-verlyn">klinkenborg-verlyn</option><option value="korsunskaia-ekaterina">korsunskaia-ekaterina</option><option value="kreider-timothy">kreider-timothy</option><option value="kronn-david">kronn-david</option><option value="krugman-jason">krugman-jason</option><option value="krupat-arnold">krupat-arnold</option><option value="kurland-justine">kurland-justine</option><option value="kyle-peter">kyle-peter</option><option value="lachapelle-mary">lachapelle-mary</option><option value="lago-eduardo">lago-eduardo</option><option value="landdeck-kevin">landdeck-kevin</option><option value="lang-allen">lang-allen</option><option value="lauinger-ann">lauinger-ann</option><option value="lauinger-joseph">lauinger-joseph</option><option value="lavigne-sharon">lavigne-sharon</option><option value="lawrence-karen-r.">lawrence-karen-r.</option><option value="lee-michelle">lee-michelle</option><option value="lee-tom">lee-tom</option><option value="leonelli-laurie-beth">leonelli-laurie-beth</option><option value="leveau-eric">leveau-eric</option><option value="lewis-linwood-j.">lewis-linwood-j.</option><option value="lewis-margot">lewis-margot</option><option value="lieber-caroline">lieber-caroline</option><option value="lieberman-sandy">lieberman-sandy</option><option value="lieu-jocelyn">lieu-jocelyn</option><option value="lin-tao">lin-tao</option><option value="liu-wan-chun">liu-wan-chun</option><option value="liu-wen">liu-wen</option><option value="long-laura-i.">long-laura-i.</option><option value="lux-thomas">lux-thomas</option><option value="lyons-robert">lyons-robert</option><option value="machugh-doug">machugh-doug</option><option value="macías-patrisia">macías-patrisia</option><option value="macmillan-brian">macmillan-brian</option><option value="macpherson-greg">macpherson-greg</option><option value="magnuson-robert">magnuson-robert</option><option value="maillo-pozo-ruben">maillo-pozo-ruben</option><option value="manago-alexander-merceditas">manago-alexander-merceditas</option><option value="mañago-alexander-merceditas">mañago-alexander-merceditas</option><option value="mandel-thomas">mandel-thomas</option><option value="mark-rona-naomi">mark-rona-naomi</option><option value="marshall-james">marshall-james</option><option value="martinez-deluca-margaret">martinez-deluca-margaret</option><option value="matthews-amy">matthews-amy</option><option value="mazmanian-victor">mazmanian-victor</option><option value="mccarthy-janelle">mccarthy-janelle</option><option value="mcdaniel-jeffrey">mcdaniel-jeffrey</option><option value="mcfarland-stephen">mcfarland-stephen</option><option value="mcghee-elena">mcghee-elena</option><option value="mcguire-liz">mcguire-liz</option><option value="mcintyre-dianne">mcintyre-dianne</option><option value="mcpherson-elizabeth">mcpherson-elizabeth</option><option value="mcree-william-d.">mcree-william-d.</option><option value="means-angelia">means-angelia</option><option value="medley-cassandra">medley-cassandra</option><option value="meira-la">meira-la</option><option value="melnick-jodi-melnick">melnick-jodi-melnick</option><option value="miller-jeffrey">miller-jeffrey</option><option value="miller-timothy">miller-timothy</option><option value="mills-joseph">mills-joseph</option><option value="mills-nicolaus">mills-nicolaus</option><option value="minsky-greta">minsky-greta</option><option value="misra-lavanya">misra-lavanya</option><option value="mitchell-rashaun">mitchell-rashaun</option><option value="mizelle-nike">mizelle-nike</option><option value="mizrahi-terry">mizrahi-terry</option><option value="moe-ruth">moe-ruth</option><option value="mofidi-shideh">mofidi-shideh</option><option value="moger-angela">moger-angela</option><option value="moos-katherine">moos-katherine</option><option value="morejon-diana-punales">morejon-diana-punales</option><option value="morris-mary">morris-mary</option><option value="mort-bari">mort-bari</option><option value="morton-brian">morton-brian</option><option value="mosolino-april-reynolds">mosolino-april-reynolds</option><option value="moudud-jamee-k.">moudud-jamee-k.</option><option value="muchmore-patrick">muchmore-patrick</option><option value="muldavin-joshua">muldavin-joshua</option><option value="murolo-priscilla">murolo-priscilla</option><option value="murray-katie">murray-katie</option><option value="muther-catherine">muther-catherine</option><option value="naka-cheiko">naka-cheiko</option><option value="naka-chieko">naka-chieko</option><option value="neely-evan">neely-evan</option><option value="negroni-maria">negroni-maria</option><option value="neskar-ellen">neskar-ellen</option><option value="neumann-david">neumann-david</option><option value="newhouse-erica">newhouse-erica</option><option value="newman-leigh">newman-leigh</option><option value="nolin-sally">nolin-sally</option><option value="novas-julie">novas-julie</option><option value="nurkse-dennis">nurkse-dennis</option><option value="oconnor-john">oconnor-john</option><option value="oconnor-stephen">oconnor-stephen</option><option value="offill-jenny">offill-jenny</option><option value="olson-leah">olson-leah</option><option value="ording-philip">ording-philip</option><option value="orlandersmith-dael">orlandersmith-dael</option><option value="ornstein-sloan-magdalena">ornstein-sloan-magdalena</option><option value="oshea-marygrace">oshea-marygrace</option><option value="oyama-sayuri-i.">oyama-sayuri-i.</option><option value="oziashvili-yekaterina">oziashvili-yekaterina</option><option value="paramaditha-intan">paramaditha-intan</option><option value="parker-matthew">parker-matthew</option><option value="partin-ted">partin-ted</option><option value="pearce-nicole">pearce-nicole</option><option value="peixoto-michael">peixoto-michael</option><option value="pelletier-carol-ann">pelletier-carol-ann</option><option value="peritz-david">peritz-david</option><option value="petty-lauren">petty-lauren</option><option value="pfordresher-jeanne1">pfordresher-jeanne1</option><option value="philipps-kris">philipps-kris</option><option value="philogene-gina">philogene-gina</option><option value="pierce-young-eddye">pierce-young-eddye</option><option value="pilkington-kevin">pilkington-kevin</option><option value="pollack-maika">pollack-maika</option><option value="pollak-max">pollak-max</option><option value="porter-karen">porter-karen</option><option value="porter-mary-a.">porter-mary-a.</option><option value="power-marilyn">power-marilyn</option><option value="prieto-josé-manuel">prieto-josé-manuel</option><option value="rainer-yvonne">rainer-yvonne</option><option value="raja-kanishka">raja-kanishka</option><option value="rakoff-joanna-smith">rakoff-joanna-smith</option><option value="redel-victoria">redel-victoria</option><option value="reich-elsa">reich-elsa</option><option value="reifler-nelly">reifler-nelly</option><option value="reilly-janet">reilly-janet</option><option value="reksten-nicholas">reksten-nicholas</option><option value="rezai-hamid">rezai-hamid</option><option value="rhodes-martha">rhodes-martha</option><option value="richards-peter">richards-peter</option><option value="risher-elise">risher-elise</option><option value="rivera-colon-edgar">rivera-colon-edgar</option><option value="robinson-sandra">robinson-sandra</option><option value="rodenbeck-judith">rodenbeck-judith</option><option value="rodgers-liz">rodgers-liz</option><option value="romano-patrick">romano-patrick</option><option value="rorandelli-tristana">rorandelli-tristana</option><option value="rosenthal-lucy">rosenthal-lucy</option><option value="rouse-shahnaz">rouse-shahnaz</option><option value="rudner-sara">rudner-sara</option><option value="ruen-kathleen">ruen-kathleen</option><option value="ryan-david">ryan-david</option><option value="sampson-efeya-ifadayo-m">sampson-efeya-ifadayo-m</option><option value="sanborn-erica">sanborn-erica</option><option value="sanchez-misael">sanchez-misael</option><option value="sanders-wayne">sanders-wayne</option><option value="sands-kristin-zahra">sands-kristin-zahra</option><option value="saptanyana-nyoman">saptanyana-nyoman</option><option value="saxon-la-rose">saxon-la-rose</option><option value="schachter-amanda">schachter-amanda</option><option value="schecter-barbara">schecter-barbara</option><option value="scheier-fanchon-miller">scheier-fanchon-miller</option><option value="schlesinger-mark-j.">schlesinger-mark-j.</option><option value="schmidt-carsten">schmidt-carsten</option><option value="schneider-ursula">schneider-ursula</option><option value="schorsch-jonathan">schorsch-jonathan</option><option value="schrader-astrid">schrader-astrid</option><option value="schultz-anthony">schultz-anthony</option><option value="schultz-tony">schultz-tony</option><option value="scotch-marmo-malia">scotch-marmo-malia</option><option value="sealander-rebecca">sealander-rebecca</option><option value="seibel-jean">seibel-jean</option><option value="seigle-samuel-b.">seigle-samuel-b.</option><option value="serafini-sauli-judith-p.">serafini-sauli-judith-p.</option><option value="seshadri-vijay">seshadri-vijay</option><option value="shafer-susan-h.">shafer-susan-h.</option><option value="shemy-deganit">shemy-deganit</option><option value="sherman-maxine">sherman-maxine</option><option value="shuford-gabriel">shuford-gabriel</option><option value="shullenberger-william">shullenberger-william</option><option value="shulman-mark-r.">shulman-mark-r.</option><option value="siff-michael">siff-michael</option><option value="silber-joan">silber-joan</option><option value="simons-lake">simons-lake</option><option value="singh-kanwal">singh-kanwal</option><option value="singh-paul">singh-paul</option><option value="sivesind-david">sivesind-david</option><option value="sizer-lyde-cullen">sizer-lyde-cullen</option><option value="slichter-jacob">slichter-jacob</option><option value="smith-michael-j.">smith-michael-j.</option><option value="smoler-fredric">smoler-fredric</option><option value="snead-pamela">snead-pamela</option><option value="sohn-sungrai">sohn-sungrai</option><option value="soiseth-alexandra">soiseth-alexandra</option><option value="sosnowy-collette">sosnowy-collette</option><option value="southgate-martha">southgate-martha</option><option value="spano-michael">spano-michael</option><option value="speer-james-w.">speer-james-w.</option><option value="speight-rico">speight-rico</option><option value="spencer-stuart">spencer-stuart</option><option value="starbuck-robin">starbuck-robin</option><option value="sternfeld-joel">sternfeld-joel</option><option value="stevens-brooke">stevens-brooke</option><option value="strype-frederick-michael">strype-frederick-michael</option><option value="swann-sterling">swann-sterling</option><option value="swoboda-philip">swoboda-philip</option><option value="tba-faculty">tba-faculty</option><option value="technical-staff-">technical-staff-</option><option value="thomas-nadeen-m.">thomas-nadeen-m.</option><option value="thom-rose-anne">thom-rose-anne</option><option value="thurber-lucy">thurber-lucy</option><option value="to-be-announced-">to-be-announced-</option><option value="tomasulo-frank">tomasulo-frank</option><option value="truax-alice">truax-alice</option><option value="turvey-malcolm">turvey-malcolm</option><option value="ulmert-megan">ulmert-megan</option><option value="uttley-lois">uttley-lois</option><option value="vahrenwald-michael">vahrenwald-michael</option><option value="vasudevan-preeti">vasudevan-preeti</option><option value="vesely-flad-rima">vesely-flad-rima</option><option value="vincenot-dash-stella">vincenot-dash-stella</option><option value="vitkin-marina">vitkin-marina</option><option value="voice-faculty-">voice-faculty-</option><option value="volpe-francine">volpe-francine</option><option value="wachs-ilja">wachs-ilja</option><option value="weil-laura">weil-laura</option><option value="weis-cathy">weis-cathy</option><option value="weiss-heidi">weiss-heidi</option><option value="wentworth-jean">wentworth-jean</option><option value="weschler-lawrence">weschler-lawrence</option><option value="westwater-kathy">westwater-kathy</option><option value="wiersma-cal">wiersma-cal</option><option value="wilbur-jennifer-scalia">wilbur-jennifer-scalia</option><option value="wilcox-sarah">wilcox-sarah</option><option value="wilford-sara">wilford-sara</option><option value="wilson-fiona">wilson-fiona</option><option value="wilson-matthew">wilson-matthew</option><option value="winter-joe">winter-joe</option><option value="winters-heather">winters-heather</option><option value="woodard-komozi">woodard-komozi</option><option value="woolfson-joseph-w.">woolfson-joseph-w.</option><option value="wright-alexandra">wright-alexandra</option><option value="wunderlich-mark">wunderlich-mark</option><option value="yamamoto-miyabi">yamamoto-miyabi</option><option value="yang-min">yang-min</option><option value="yannelli-john-a.">yannelli-john-a.</option><option value="yates-jonathan">yates-jonathan</option><option value="yin-mali">yin-mali</option><option value="yionoulis-evan">yionoulis-evan</option><option value="yoo-mia">yoo-mia</option><option value="yoon-paul">yoon-paul</option><option value="young-thomas">young-thomas</option><option value="zambreno-kate">zambreno-kate</option><option value="zerfas-francine">zerfas-francine</option><option value="zerner-charles">zerner-charles</option><option value="zevin-dan">zevin-dan</option><option value="zollar-keisha">zollar-keisha</option><option value="zoref-carol">zoref-carol</option><option value="zuern-elke">zuern-elke</option></select></div>');
  
  $('.event-form').append('<input name="login" type="hidden" value="$user"/><input name="password" type="hidden" value="$password"/><input name="client" type="hidden" value="$client"/><input name="type" type="hidden" value="page"/><input name="action" type="hidden" value="edit"/>');
  $('.event-form').each(function(i,v) { $('#deleted_events').clone().removeAttr('id').appendTo( $(this) ); $(this).append('<input type="submit" name="submit" class="btn btn-info" value="Copy data"/>'); });
  $('.event-form').each(function(i,v) {\$(v).append('<div class="btn-group pull-right"><input type="submit" name="submit" class="btn btn-success" value="Enable and Tag '+ \$(v).find('h4').text() +'"/><input type="submit" name="submit" class="btn btn-warning" value="Reject"/></div>')});
  $('#deleted_events').remove();

  $('.event-form .btn-success').click(function(e) {
    if ( $(this).is('.disabled') ) { e.preventDefault(); return false; }
    $(this).closest('form').attr('action','enable.php');
    $(this).closest('form').append('<div class="label label-success pull-right">Enable request sent</div>');
    $(this).closest('form').find('.btn').addClass('disabled');
  });
  $('.event-form .btn-warning').click(function(e) {
    if ( $(this).is('.disabled') ) { e.preventDefault(); return false; }
    $(this).closest('form').attr('action','reject.php');
    $(this).closest('form').append('<div class="label label-important pull-right">Reject request sent</div>');
    $(this).closest('form').find('.btn').addClass('disabled');
  });
  $('.event-form .btn-info').click(function(e) {
    if ( $(this).is('.disabled') ) { e.preventDefault(); return false; }
    $(this).closest('form').attr('action','copyData.php');
    $(this).closest('form').append('<div class="label label-important pull-right">Copy request sent</div>');
    $(this).closest('form').find('.btn').addClass('disabled');
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
  $del_folder = $client->read ( array ('authentication' => $auth, 'identifier' => array ('type' => 'folder', id => $deleted_folder ) ) );
  if ($del_folder->readReturn->success == 'true') {
    $del_asset = ( array ) $del_folder->readReturn->asset->folder;
    $deleted_events = $del_asset["children"]->child;
    echo '<select name="deleted_event" id="deleted_events" class="deleted_events"> <option value="">Select a deleted event</option>';
    foreach ($deleted_events as $del_event) {
      echo "<option value='".$del_event->id."'>".$del_event->path->path."</option>";
    }
    echo '</select>';
  } else {
    if (!$cron) {echo "Couldn't read deleted events folder.";}
  }
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
      echo '<form class="event-form clearfix" method="POST" target="result">';
        echo '<input type="hidden" name="id" value="'.$asset['id'].'"/>';
        echo '<h4><a target="_blank" href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=page&">'.$asset['metadata']->title.'</a></h4>';
        echo '<div>'.$asset['path'].$name.'</div>';
        echo '<div class="k">'.gmdate("D M dS, H:i", $begin).'</div> - <div class="k">'.gmdate('D M dS, H:i', $end).'</div>'.($recurring == 'False' ? '' : '<div class="label label-info">Recurring</div>').' <a class="label label-success" target="_blank" href="'.$eventsource.'">Source</a>';
        echo '<div><strong>Location:</strong> '.$location.'</div>';
        echo '<div><strong>Sponsor:</strong> '.$sponsor.'</div>';
        echo '<div><strong>Type:</strong> '.$type.'</div>';
        echo '<div style="max-width:600px; background:#dde;">'.$asset['metadata']->summary.'</div>';
        echo '<div style="max-width:600px;">'.$content.'</div>';
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
