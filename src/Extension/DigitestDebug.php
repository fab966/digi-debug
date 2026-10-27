<?php
namespace Digitest\Plugin\System\DigitestDebug\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;

class DigitestDebug extends CMSPlugin {

    public function onAfterRender(){
		
        $app = Factory::getApplication();
        if ($app->isClient('administrator')) { return; }
		
    	$user      = Factory::getUser();
    	$userId    = $user->id;
    	$myUserIds = $this->params->get('myUserIds', '');

    	$myUserIdArray = array_map('trim', explode(',', (string)$myUserIds));
    
    	if ($userId === 0 || !in_array((string)$userId, $myUserIdArray)) {
        	return;
    	}
		
		$jVersion = new \Joomla\CMS\Version();

        $doc				= Factory::getDocument();
		$option				= $app->input->getCmd('option', '');
		$view				= $app->input->getCmd('view', '');
		$layout 			= $app->input->getCmd('layout', 'undefined');
		$page 				= $app->input->getCmd('page', 'undefined');
		$task 				= $app->input->getCmd('task', 'undefined');
		
		if($option == "com_content"){
			$temp				= $app->input->getString('id', '');
			$temp				= explode(':', $temp);
			$art				= $temp[0];
		}else{
			$art				= "Not an Article";
		}
		
		// Shutdown with some Extensions
		if($option == "com_eyesite")
		{
			return;
		}
		
		$menu_id 			= $app->getMenu()->getActive()->id;
		
		$lang 				= Factory::getLanguage();
		$lang_tag			= substr($lang->getTag(),0,2);
		//$user				= Factory::getUser();	
		
		$menu				= $app->getMenu()->getActive();
		$page_title			= $doc->getTitle();
  		$page_class			= '';
  		$page_heading		= '';
  		$blog_class			= '';
  		if (is_object($menu)){
			$page_class		= $menu->getParams()->get('pageclass_sfx');
			$page_heading	= $menu->getParams()->get('page_heading');
			$blog_class		= $menu->getParams()->get('blog_class');
		}
		
		// Get some Params ==========================================================
		$myUserIds			= $this->params->get('myUserIds', 0);
		$ModPosCounter		= $this->params->get('ModPosCounter', 0);
		$showTemplateParams	= $this->params->get('showTemplateParams', 0);
		$textColor			= $this->params->get('textColor', '#444');
		$theme				= $this->params->get('theme', 0);
		$bkgColor			= $this->params->get('bkgColor', '#ffffff');		
		$ModCounter			= count(ModuleHelper::getModules($ModPosCounter));
		
		if($theme == 1){$textColor = '#ffffff';$bkgColor = '#444';};
		
		// Set CSS Style ============================================================
		$style				= 'div#digi-debug-cnt{
									display:flex;
									justify-content: space-between;
									color:' . $textColor .';
									text-align:left;
									margin:0;
									padding:0 24px;
									font-family:Geneva,Courier New,sans-serif;
									font-size:14px;
									box-sizing:border-box;
									line-height:22px;
									min-height:350px;
									border-top:1px solid #CCC;
									background-color:' . $bkgColor . ';
								}
								div.digi-debug-item{
									width:48%;
									padding-bottom: 36px;
								}
								span.yes{
									background-color: #48aa48;
  									color: #FFF;
  									padding: 0 6px;
  									border-radius: 6px;
								}
								span.no{
									background-color: #d75555;
  									color: #FFF;
  									padding: 0 6px;
  									border-radius: 6px;
								}
								@media only screen and (max-width:760px){
									div#digi-debug-cnt{
										flex-direction:column;
									}
									div.digi-debug-item{
										width:100%;
										padding-bottom: 12px;
									}
								}
									';
		
		
		// Get Category ID ==========================================================
		$db = Factory::getDBO();
		if ($view == "category") {
			$category_id = $app->input->getCmd('id');
		}elseif ($view == "article"){
			$query = "SELECT catid FROM #__content WHERE id = ".$art;
			 $db->setQuery($query);
			$category_id = $db->loadResult();
		}else{
			$category_id = "undefined";
		}
		
		// Check for commons PlugIn & Extensions ====================================		
		$checkExt = function($type, $name, $folder = 'system') {
    		$enabled = ($type === 'component') ? \Joomla\CMS\Component\ComponentHelper::isEnabled($name, true) : \Joomla\CMS\Plugin\PluginHelper::isEnabled($folder, $name);
    		return $enabled ? '<span class="yes">Yes</span>' : '<span class="no">No</span>';
		};
		
		// Get Template params (if Digitest Templates) ==============================
		$templateParam		= $app->getTemplate(true);
		$params				= $templateParam->params;
		
		$responsivemenu		= $params->get('responsivemenu', '0');
		$setjquery			= $params->get('setjquery', '0');
		$setmootools		= $params->get('setmootools', '0');
		$setbootstrap		= $params->get('setbootstrap', '0');
		$testMessage		= $params->get('testMessage', '0');
		$setToTop			= $params->get('setToTop', '0');
		$enableAnalytics	= $params->get('enableAnalytics', '0');
		$enableRemarketing	= $params->get('enableRemarketing', '0');        
		$setscrollReveal	= $params->get('setscrollReveal', '0');	
		
		// Get USER device and browser ==============================================  
		$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
		$browser = "Unknown Browser";
		$platform = "Unknown OS";

		// Operating System Mapping (the most common today)
		$os_array = [
			'/windows|win32/i'      => 'Windows',
			'/macintosh|mac os x/i' => 'macOS',
			'/iphone|ipad|ipod/i'   => 'iOS',
			'/android/i'            => 'Android',
			'/linux/i'              => 'Linux'
		];

		// Browser Mapping (order matters: Edge and Chrome are similar)
		$browser_array = [
			'/edg/i'      => 'Edge',
			'/chrome/i'   => 'Chrome',
			'/firefox/i'  => 'Firefox',
			'/safari/i'   => 'Safari',
			'/opera|opr/i' => 'Opera',
			'/msie|trident/i' => 'Internet Explorer'
		];

		foreach ($os_array as $regex => $value) {
    		if (preg_match($regex, $ua)) { $platform = $value; break; }
		}

		foreach ($browser_array as $regex => $value) {
			if (preg_match($regex, $ua)) { $browser = $value; break; }
		}
		
		// Simplified Mobile Detection (without Mobile_Detect)
		$isMobile = preg_match('/mobile|android|iphone|ipad|ipod/i', $ua) ? '📱 Mobile' : '💻 Desktop';
        
        // Verifica Utente (Corretta)
        $user = $app->getIdentity();
        $userId = (int) $user->id;
        $myUserIds = $this->params->get('myUserIds', '');
        $myUserIdArray = array_map('trim', explode(',', $myUserIds));

        // Compiler =================================================================
		$html = Factory::getApplication()->getBody();
        
		$contenuto = '<div id="digi-debug-cnt">';
		$contenuto .= '<div class="digi-debug-item">';		
		$contenuto .= '<br /><b>CMS Output Variables & Parameters</b>';
		$contenuto .= '<br />Option = ' . $option;
		$contenuto .= '<br />View = ' . $view;
		$contenuto .= '<br />Layout = ' . $layout;
		$contenuto .= '<br />Page = ' . $page;
		$contenuto .= '<br />Task = ' . $task;		
		$contenuto .= '<br />Menu ID = ' . $menu_id;	
		$contenuto .= '<br />Element ID (Art) = ' . $art;
		$contenuto .= '<br />Category ID = ' . $category_id;
		
		$contenuto .= '<br />Page Title = ' . $page_title;
		$contenuto .= '<br />Page Heading = ' . $page_heading;
		$contenuto .= '<br />Page Class = ' . $page_class;
		$contenuto .= '<br />Blog Class = ' . $blog_class;
		
		$contenuto .= '<br />---------------------------';
		$contenuto .= '<br />Language = ' . $lang_tag;
		$contenuto .= '<br />Logged User ID = ' . $userId;
		
		$contenuto .= '<br />---------------------------';		
		$contenuto .= '<br />Module Counter (pos: '. $ModPosCounter .') = ' . $ModCounter;
		
		$contenuto .= '<br />---------------------------';
		$contenuto .= '<br /><b>USER Agent</b>';
		$contenuto .= '<br />Device Type = ' . $isMobile;
		$contenuto .= '<br />Platform = ' . $platform;
		$contenuto .= '<br />Browser = ' . $browser;	
		
		$contenuto .= '<br /><br /></div>';
		$contenuto .= '<div class="digi-debug-item">';
		
		if($showTemplateParams){		
			$contenuto .= '<br /><b>Custom Template Params (Digitest)</b>';
			$contenuto .= '<br />Scroll Reveal = ' . $setscrollReveal;
			$contenuto .= '<br />Test Messages = ' . $testMessage;
			$contenuto .= '<br />Pulsante Torna su = ' . $setToTop;
			$contenuto .= '<br />---------------------------';
		}
		
		$contenuto .= '<br /><b>Common Extensions Status (Installed &amp; Active)</b>';
		$contenuto .= '<br />OpenGraph = ' . $checkExt('plugin', 'phocaopengraph', 'content');
		$contenuto .= '<br />Page Title Extended = ' . $checkExt('plugin', 'page_title_extended');
		$contenuto .= '<br />Cookies Policy 357 = ' . $checkExt('plugin', 'cookiespolicynotificationbar');
		$contenuto .= '<br />Digitest Debug = ' . $checkExt('plugin', 'digitestdebug');
		$contenuto .= '<br />Digitest Secure = ' . $checkExt('plugin', 'digisecure');
		$contenuto .= '<br />Akeeba BKP = ' . $checkExt('component', 'com_akeebabackup');
		$contenuto .= '<br />Engage Box = ' . $checkExt('component', 'com_rstbox');
		$contenuto .= '<br />Convert Form = ' . $checkExt('component', 'com_convertforms');
		$contenuto .= '<br />Brute Force Stop = ' . $checkExt('component', 'com_bfstop');
		$contenuto .= '<br />OS Map = ' . $checkExt('component', 'com_osmap');
		
		$contenuto .= '<br />---------------------------';
		$contenuto .= '<br /><b>System Data</b>';
		$contenuto .= '<br />Joomla Version = ' . $jVersion->getShortVersion();
		$contenuto .= '<br />PHP Version = ' . phpversion();
		
		// 1. Recupera il timestamp di inizio (microtime) memorizzato da Joomla
		$startTime = $_SERVER['REQUEST_TIME_FLOAT'];

		// 2. Calcola la differenza con il momento attuale
		$executionTime = microtime(true) - $startTime;

		// 3. Formatta il risultato (es. 3 decimali) e aggiungilo all'output
		$contenuto .= '<br />---------------------------';
		$contenuto .= '<br /><b>Performances</b>';
		$contenuto .= '<br />Risposta del CMS: ' . number_format($executionTime, 3) . ' secondi';		
		
		$db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
		$numQueries = $db->getCount();
		
		// Se le query sono troppe (> 100), segnalalo in rosso
		$qColor = ($numQueries > 100) ? 'red' : 'inherit';
		
		$contenuto .= '<br />Query DB: <span style="color:'.$qColor.'">' . $numQueries . '</span>';	

		// Consumo di memoria RAM
		$memPeak = memory_get_peak_usage(true) / 1024 / 1024;
		$memLimit = (int) ini_get('memory_limit'); // Prende il limite del server (es. 128)
		$mColor = ($memPeak > ($memLimit * 0.8)) ? 'red' : 'inherit'; // Rosso se superi l'80% del limite
		$contenuto .= '<br />RAM Peak: <span style="color:'.$mColor.'">' . number_format($memPeak, 2) . ' MB</span> (Limit: ' . $memLimit . 'M)';
		
		$contenuto .= '<br />---------------------------';		
		
		if(version_compare(PHP_VERSION, '8.0.0', '<')) {
			$contenuto .= '<br/ ><span style="color:red;">It is time to upgrade your server to PHP8 !!!</span>';
		}
		
        $contenuto .= '<br/ >PLG Digitest Debug ver. 3.1.1';
		$contenuto .= '<br /></div></div>';

        // Final injection (Correct for J6)
        $doc = Factory::getDocument();
        $doc->addStyleDeclaration($style);

        // Prepare the complete CSS block with <style> tags
		$styleTag = '<style type="text/css">' . $style . '</style>';

		// Retrieve the HTML of the page
		$html = $app->getBody();		
		
		// --- AGGIUNTA: Iniezione dello script MIME Debug ---
        if ($this->params->get('check_missing_assets', 0) == 1) {
            $debugScript = "<script>
            window.addEventListener('error', function(event) {
                var target = event.target || event.srcElement;
                var isResourceError = target instanceof HTMLScriptElement || 
                                      target instanceof HTMLLinkElement || 
                                      target instanceof HTMLImageElement;
                if (isResourceError) {
                    console.error('%c[DigitestDebug] Risorsa mancante o bloccata (MIME):', 'background: #d9534f; color: white; padding: 2px 6px; font-weight: bold; border-radius: 3px;', {
                        tipoTag: target.tagName,
                        urlFallito: target.src || target.href
                    });
                }
            }, true);
            </script>";

            $styleTag = $debugScript . $styleTag;
        }
		// --------------------------------------------------		
		

		// Injecting the STYLE into the <head> (if present)
		if (str_contains($html, '</head>')) {
    		$html = str_replace('</head>', $styleTag . '</head>', $html);
		}

		// Injecting CONTENT before </body>
		if (str_contains($html, '</body>')) {
    		$html = str_replace('</body>', $contenuto . '</body>', $html);
		}

		// Update the page body only once
		$app->setBody($html);
    }
}