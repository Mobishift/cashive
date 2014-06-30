<?php

class Faq extends Eloquent {

	protected $table = 'faqs';

	protected $fillable = array('question', 'answer', 'sort_order');
}