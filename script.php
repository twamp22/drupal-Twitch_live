<?php
/**
 * This is some PHP for Drupal that I have written to embed one(randomly) of the user's twitch streams.
 * If no Drupal users are live on Twitch, show one of the top Twitch channels.
 *
 * This script gets all available Twitch account channels from HybridAuth using Drupal's user entity.
 * If any of those channels are live, it will randomly select one to embed.
 * If no Drupal users are live on Twitch, show one of the top Twitch channels.
 *
 * @author     Thomas "Twamp" Wright <twamp@3xa-gaming.net>
 */

$users = entity_load('user');
$usernames = array();
foreach($users as $id => $user){
	$usernames[$user->uid] = $user->name;
	$user_fields = user_load($user->uid);
	if (isset($user_fields->field_twitch_channel['und']['0']['value'])) {
		$twitchusers = $user_fields->field_twitch_channel['und']['0']['value'];
		if ($twitchusers != '') {
			$tUser_array[] = $twitchusers;
		}
	}
}
$channels = array_unique($tUser_array);
$inc = checkStreamStatus($channels);
if (is_array($inc)) {
	$rNumber = count($inc) - 1;
	$rChannel = $inc[rand(0,$rNumber)];
	$tEmbedVideo = '<center><iframe src="http://www.twitch.tv/' . $rChannel . '/embed" frameborder="0" scrolling="no" height="100%" width="100%"></iframe><br>';
	$tEmbedFooter = 'Live Twitch Channel: <a class="button" href="http://www.twitch.tv/' . $rChannel . '">' . ucfirst($rChannel) . '</a></center>';
	$tEmbedFull = $tEmbedVideo . $tEmbedFooter;
} else {
	$topTwitch = array('sodapoppin','trumpsc','kittyplaysgames','maximusblack','lirik','sing_sing','walshy','itshafu','lethalfrag','dansgaming','phantoml0rd','manvsgame','towelliee','summit1g','wowhobbs','totalbiscuit','teamsp00ky','syndicate','bacon_donut','ms_vixen','trick2g','defrancogames','seriousgaming','streamerhouse','captainsparklez');
	$cev = checkStreamStatus($topTwitch);
	$rNumber = count($cev) - 1;
	$rChannel = $cev[rand(0,$rNumber)];
	$tEmbedVideo = '<center><iframe src="http://www.twitch.tv/' . $rChannel . '/embed" frameborder="0" scrolling="no" height="378" width="620"></iframe><br>';
	$tEmbedFooter = 'No registered live channels!<br>Streaming <a class="button" href="http://www.twitch.tv/' . $rChannel . '">' . ucfirst($rChannel) . '</a></center>';
	$tEmbedFull = $tEmbedVideo . $tEmbedFooter;
}
print($tEmbedFull);

function checkStreamStatus($tChan) {
	if (is_array($tChan)) {
		foreach($tChan as $cChan) {
			$streamArray = json_decode(@file_get_contents('https://api.twitch.tv/kraken/streams?channel=' . $cChan), true);
			foreach ($streamArray['streams'] as $stream) {
				if($stream['_id'] != null){
					$name = $stream['channel']['name'];
					$tChan = $name;
					if (!isset($livechannels)) {
						$livechannels = array();
					}
					array_push($livechannels, $name);
				} else {
					$tChan = $stream . " OFFLINE<br>";
					$livechannels = array();
				}
			}
		}
		return $livechannels;
	} else {
		$tChan = null;
		return $tChan;
	}
}
?>