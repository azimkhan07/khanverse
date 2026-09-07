<?php
$rows = DB::table("menu_items")->select("panel","title",DB::raw("count(*) as c"))->groupBy("panel","title")->havingRaw("count(*) > 1")->get();
echo "DUP_MENU_ROWS=".$rows->count().PHP_EOL;
foreach($rows as $r){ echo $r->panel."|".$r->title."|".$r->c.PHP_EOL; }
$u = DB::table("users")->select("email",DB::raw("count(*) as c"))->groupBy("email")->havingRaw("count(*) > 1")->get();
echo "DUP_EMAIL=".$u->count().PHP_EOL;
foreach($u as $r){ echo "email|".$r->email."|".$r->c.PHP_EOL; }
$p = DB::table("users")->whereNotNull("phone")->select("phone",DB::raw("count(*) as c"))->groupBy("phone")->havingRaw("count(*) > 1")->get();
echo "DUP_PHONE=".$p->count().PHP_EOL;
foreach($p as $r){ echo "phone|".$r->phone."|".$r->c.PHP_EOL; }

