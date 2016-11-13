<?php
namespace View;

class UserCP extends Base
{

	public static function showMenu($menu="")
	{
		\Base::instance()->set('panel_menu', $menu);
		return \Template::instance()->render('usercp/menu.html');
	}

	public static function msgInOutbox($data, $select="inbox")
	{
		if ( $select == "outbox" )
		{
			$select = "Outbox";
			$person_is = "Recipient";
			$date_means = "Sent";
		}
		else
		{
			$select = "Inbox";
			$person_is = "Sender";
			$date_means = "Received";
		}
		$f3 = \Base::instance();

		$f3->set('messages', $data);
		$f3->set('WHICH', $select);
		$f3->set('PERSON_IS', $person_is);
		$f3->set('DATE_MEANS', $date_means);

		return \Template::instance()->render('usercp/messaging.inout.html');
	}

	public static function msgRead($data)
	{
		\Base::instance()->set('message', $data);
		\Base::instance()->set('forward',($data['sender_id']==$_SESSION['userID']) );
		
		return \Template::instance()->render('usercp/messaging.read.html');
	}

	public static function msgWrite($data)
	{
		\Base::instance()->set('write_data', $data);
		return \Template::instance()->render('usercp/messaging.write.html');
	}
	
	public static function libraryBookFavEdit($data, $params)
	{
		\Registry::get('VIEW')->javascript( 'head', TRUE, "jquery.are-you-sure.js" );
		\Base::instance()->set('data', $data);
		\Base::instance()->set('block', $params[0]);
		\Base::instance()->set('returnpath', $params['returnpath']);
		\Base::instance()->set('saveError', @$params['error']);
		
		return \Template::instance()->render('usercp/library.editBookFav.html');
	}
	
	public static function libraryListBookFav(array $data, array $sort, array $extra)
	{
		\Registry::get('VIEW')->javascript( 'head', TRUE, "controlpanel.js.php?sub=confirmDelete" );

		\Base::instance()->set('libraryEntries', $data);
		\Base::instance()->set('sort', $sort);
		\Base::instance()->set('extra', $extra);
		return \Template::instance()->render('usercp/library.html');
	}
	
	public static function upperMenu(array $menu, array $counter, $path, $sub)
	{
		\Base::instance()->set('menu_upper', $menu);
		\Base::instance()->set('counter', $counter);
		\Base::instance()->set('sub', $sub);
		\Base::instance()->set('path', $path);

		return \Template::instance()->render('usercp/menu_upper.html');
	}
	
	public static function feedbackHome(array $data)
	{
		\Registry::get('VIEW')->javascript( 'head', TRUE, "piechart.js" );
		
		\Base::instance()->set('stats', $data);
		return \Template::instance()->render('usercp/feedback.home.html');
	}

	public static function feedbackListReviews(array $data, array $sort, array $extra)
	{
		\Registry::get('VIEW')->javascript( 'head', TRUE, "controlpanel.js.php?sub=confirmDelete" );

		\Base::instance()->set('feedbackEntries', $data);
		\Base::instance()->set('sort', $sort);
		\Base::instance()->set('extra', $extra);
		return \Template::instance()->render('usercp/feedback.html');
	}

	public static function libraryFeedbackEdit($data, $params)
	{
		\Registry::get('VIEW')->javascript( 'head', TRUE, "jquery.are-you-sure.js" );
		\Base::instance()->set('data', $data);
		\Base::instance()->set('block', $params[0]);
		\Base::instance()->set('direction', $params[1]);
		\Base::instance()->set('returnpath', $params['returnpath']);
		\Base::instance()->set('saveError', @$params['error']);
		
		return \Template::instance()->render('usercp/feedback.edit.html');
	}
	
	public static function settingsChangePW($feedback)
	{
		\Base::instance()->set('feedback', $feedback);
		
		return \Template::instance()->render('usercp/changepw.html');
	}
	
}
