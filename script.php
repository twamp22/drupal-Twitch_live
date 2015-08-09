<?php
/**
 * This is some PHP for Drupal that I have written to embed one(randomly) of the user's twitch streams.
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
	$rChannel = $inc[rand(0,(count($inc) - 1))];
		$tEmbedVideo = '<center><iframe src="http://www.twitch.tv/' . $rChannel . '/embed" frameborder="0" scrolling="no" height="468" width="778"></iframe>';
		$tEmbedFooter = '<div class="alert-success">One of our registered users is live right now!</div>Thanks for watching: <a class="button" href="http://www.twitch.tv/' . $rChannel . '">' . ucfirst($rChannel) . '</a><hr>We are always looking for more members and guests for our site and community! If you would like your stream to be shown on this page when you go live, all you have to do is register! If you are interesting in joining 3xA-Gaming we are always accepting applications! If you would like to learn more about us just click "About 3xA-Gaming" above!</center>';
	$tEmbedFull = $tEmbedVideo . $tEmbedFooter;
} else {
	$topTwitch = array('sodapoppin','trumpsc','kittyplaysgames','maximusblack','lirik','sing_sing','walshy','itshafu','lethalfrag','dansgaming','phantoml0rd','manvsgame','towelliee','summit1g','wowhobbs','totalbiscuit','teamsp00ky','syndicate','bacon_donut','ms_vixen','trick2g','defrancogames','seriousgaming','streamerhouse','captainsparklez');
	$cev = checkStreamStatus($topTwitch);
	if (is_array($cev)) {
		$rChannel = $cev[rand(0,(count($cev) - 1))];
		$tEmbedVideo = '<center><iframe src="http://www.twitch.tv/' . $rChannel . '/embed" frameborder="0" scrolling="no" height="468" width="778"></iframe>';
		$tEmbedFooter = '<div class="alert-warning">None of our registered users are live right now!</div><a class="button" href="http://www.twitch.tv/' . $rChannel . '">' . ucfirst($rChannel) . '</a> is live however!<br>This is one of the top 25 Twitch streamers!<hr><font color="#d33b3c">Normally you would see an embedded Twitch.tv live stream here, but none of the users registered here are live streaming right now!</font><br><br>We are always looking for more members and guests for our site and community! If you would like your stream to be shown on this page when you go live, all you have to do is register! If you are interesting in joining 3xA-Gaming we are always accepting applications! If you would like to learn more about us just click "About 3xA-Gaming" above!</center>';
		$tEmbedFull = $tEmbedVideo . $tEmbedFooter;
	} else {
		$tEmbedFull = '<div class="alert-error"><center>No users that we know are live right now!<div class="delete-button"></div></center></div><hr><center><font color="#d33b3c">Normally you would see an embedded Twitch.tv live stream here, but none of the users registered here are live streaming right now! We show the top 25 Twitch streamers when our users are not live, but none of those channels are live right now either!</font><br><br>We are always looking for more members and guests for our site and community! If you would like your stream to be shown on this page when you go live, all you have to do is register! If you are interesting in joining 3xA-Gaming we are always accepting applications! If you would like to learn more about us just click "About 3xA-Gaming" above!</center>';
	}
}
print($tEmbedFull);

function checkStreamStatus($tChan) {
	if (is_array($tChan)) {
		foreach($tChan as $cChan) {
			$streamArray = json_decode(@file_get_contents('https://api.twitch.tv/kraken/streams?channel=' . $cChan), true);
			foreach ($streamArray['streams'] as $stream) {
				if($stream['_id'] != null){
					$name = $stream['channel']['name'];
					if (!isset($livechannels)) {
						$livechannels = array();
					}
					array_push($livechannels, $name);
				}
			}
		}
		return $livechannels;
	} else {
		return NULL;
	}
}
?>