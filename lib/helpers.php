<?php
function set_meta($title=null,$desc=null,$image=null,$slug=null){
  if($title) $GLOBALS['_PAGE_META']['title']=$title;
  if($desc)  $GLOBALS['_PAGE_META']['desc']=$desc;
  if($image) $GLOBALS['_PAGE_META']['image']=$image;
  if($slug)  $GLOBALS['_PAGE_META']['slug']=$slug;
}
function pictureTag(string $base, string $alt='', string $sizes='100vw', string $class=''){
  $r = $_SERVER['DOCUMENT_ROOT'].'/assets/img/';
  $fallback = file_exists($r."{$base}.jpg") ? "/assets/img/{$base}.jpg"
            : (file_exists($r."{$base}.png") ? "/assets/img/{$base}.png"
            : (file_exists($r."{$base}.svg") ? "/assets/img/{$base}.svg" : "/assets/img/{$base}.jpg"));
  echo '<picture class="'.htmlspecialchars($class).'">';
  echo '<source type="image/avif" srcset="/assets/img/'.htmlspecialchars($base).'.avif" sizes="'.htmlspecialchars($sizes).'">';
  echo '<source type="image/webp" srcset="/assets/img/'.htmlspecialchars($base).'.webp" sizes="'.htmlspecialchars($sizes).'">';
  echo '<img src="'.htmlspecialchars($fallback).'" alt="'.htmlspecialchars($alt).'" loading="lazy" style="border-radius:20px">';
  echo '</picture>';
}