#!/usr/bin/perl

use Test::More tests => 1;
BEGIN {
	use lib '../lib'; 
	use_ok('Vitacilina')
};

$| = 1;

use lib '../lib';
use Vitacilina;
use Data::Dumper;

my $p = Vitacilina->new(
	config	=>	'../config/ec.yaml',
	template	=> '../templates/main.tt',
);

$p->render;
