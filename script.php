<?php

/**
 * This is some PHP for Drupal that I have written to embed one(randomly) of the user's twitch streams.
 *
 * This script gets all available Twitch account name from the custom user profile field (field_twitch_channel). 
 * If any of those accounts are live, it will randomly select one to embed.
 * If no user streams are live, it will embed twitch.tv/twitch.
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
	$tEmbedVideo = '<div class="one_three"><iframe src="http://www.twitch.tv/' . $rChannel . '/embed" frameborder="0" scrolling="no" height="100%" width="100%"></iframe>';
	$tEmbedFooter = '<div class="two_three"><div class="action-box-dark">Live Twitch Channel: <a class="button" href="http://www.twitch.tv/' . ucfirst($rChannel) . '">' . ucfirst($rChannel) . '</a></div></div></div>';
	$tEmbedFull = $tEmbedVideo . $tEmbedFooter;
} else {
	$rChannel = 'twitch';
	$tEmbedVideo = '<div class="one_three"><div class="blockquote">No Live Twitch Channels! Showing <a href="http://twitch.tv/twitch">Twitch.tv/Twitch</a> Channel.</div><iframe src="http://www.twitch.tv/' . $rChannel . '/embed" frameborder="0" scrolling="no" height="180" width="300"></iframe>';
	$tEmbedFull = $tEmbedVideo;
}
	print($tEmbedFull);
function checkStreamStatus($tChan) {
	global $livechannels;
	If (is_array($tChan)) {
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
					$livechannels = 0;
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