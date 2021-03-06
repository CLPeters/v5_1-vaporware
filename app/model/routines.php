<?php
namespace Model;
/*
	This class offers database routines
*/
class Routines extends Base {
	
//	public function cacheCategories(int $catID=0)
	public function cacheCategories($catID=0)
	{
		// clear stats of affected categories
		$sql = "UPDATE `tbl_categories` SET `stats` = NULL";
		if ($catID>0) $sql .=  " WHERE `cid` = {$catID};";
		$this->exec($sql);
		
		$categories = new \DB\SQL\Mapper($this->db, $this->prefix.'categories' );
		
		$sql = "SELECT C.cid, C.category, COUNT(DISTINCT S.sid) as counted, 
					GROUP_CONCAT(DISTINCT C1.category SEPARATOR '||' ) as sub_categories, 
					GROUP_CONCAT(DISTINCT C1.stats SEPARATOR '||' ) as sub_stats
			FROM `tbl_categories`C 
				INNER JOIN (SELECT leveldown FROM `tbl_categories` WHERE `stats` = '' ORDER BY leveldown DESC LIMIT 0,1) c2 ON ( C.leveldown = c2.leveldown )
				LEFT JOIN `tbl_stories_categories`SC ON ( C.cid = SC.cid )
				LEFT JOIN `tbl_stories`S ON ( S.sid = SC.sid )
				LEFT JOIN `tbl_categories`C1 ON ( C.cid = C1.parent_cid )
			GROUP BY C.cid";
			
		do {
			$items = $this->exec( $sql );
			$change = FALSE;
			foreach ( $items as $item)
			{
				if ( $item['sub_categories']==NULL ) $sub = NULL;
				else
				{
					$sub_categories = explode("||", $item['sub_categories']);
					$sub_stats = explode("||", $item['sub_stats']);
					$sub_stats = array_map("json_decode", $sub_stats);
					foreach( $sub_categories as $key => $value )
					{
						$item['counted'] += $sub_stats[$key]->count;
						$sub[$value] = $sub_stats[$key]->count;
					}
				}
				$stats = json_encode([ "count" => (int)$item['counted'], "cid" => $item['cid'], "sub" => $sub ]);
				unset($sub);
				
				$categories->load(array('cid=?',$item['cid']));
				$categories->stats = $stats;
				$categories->save();
				
				$change = ($change) ? : $categories->changed();
			}
		} while ( $change != FALSE );
	}
	
	public static function dropUserCache($module="", $uid=NULL)
	{
		$modules = [ "feedback", "messaging" ];
		if ( in_array($module, $modules) )
		{
			$sql = "UPDATE `tbl_users`U SET U.cache_{$module} = '' WHERE U.uid =";
			if ( $uid )
				parent::instance()->exec($sql . " :uid;", [ ":uid" => $uid ]);
			
			else
				parent::instance()->exec($sql . " {$_SESSION['userID']};");
		}
	}
	
	public function noteReview ( $storyID )
	{
		$sql = "SELECT IF(A.realname='',A.nickname,A.realname) as mailname, email
					FROM `tbl_users`A
						INNER JOIN `tbl_stories_authors`Rel ON ( A.uid = Rel.aid )
					WHERE Rel.sid = :sid AND Rel.type = 'M' AND A.alert_feedback = 1;";
		return $this->exec($sql, [":sid" => $storyID]);
	}
	
	public function noteComment ( $feedbackID )
	{
		$sql = "SELECT IF(A.realname='',A.nickname,A.realname) as mailname, email
					FROM `tbl_users`A
						INNER JOIN `tbl_feedback`F ON ( A.uid = F.writer_uid AND F.writer_uid > 0 )
					WHERE F.fid = :fid AND F.type = 'C' AND A.alert_comment = 1;";
		return $this->exec($sql, [":fid" => $feedbackID]);
	}
	
}
