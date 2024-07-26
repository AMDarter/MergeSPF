<?php

namespace AMDarter;

use PHPUnit\Framework\TestCase;
use AMDarter\MergeSPF;

class MergeSPFTest extends TestCase
{
    
    public function testMergeIdenticalRecords()
    {
        $spfRecord1 = 'v=spf1 a mx -all';
        $spfRecord2 = 'v=spf1 a mx -all';
        $merged = MergeSPF::merge($spfRecord1, $spfRecord2);
        $this->assertEquals('v=spf1 a mx -all', $merged);
    }

    public function testMergeDifferentRecords()
    {
        $spfRecord1 = 'v=spf1 a mx -all';
        $spfRecord2 = 'v=spf1 a ip4:192.168.1.1 ~all';
        $merged = MergeSPF::merge($spfRecord1, $spfRecord2);
        $this->assertEquals('v=spf1 a mx ip4:192.168.1.1 -all', $merged);
    }

    public function testMergeWithModifiers()
    {
        $spfRecord1 = 'v=spf1 a mx -all redirect=_spf.example.com';
        $spfRecord2 = 'v=spf1 a ip4:192.168.1.1 ~all exp=example.com';
        $merged = MergeSPF::merge($spfRecord1, $spfRecord2);
        $this->assertEquals('v=spf1 a mx ip4:192.168.1.1 -all redirect=_spf.example.com exp=example.com', $merged);
    }

    public function testMergeWithDefaultOnEmpty()
    {
        $spfRecord1 = '';
        $spfRecord2 = '';
        $default = 'v=spf1 -all';
        $merged = MergeSPF::merge($spfRecord1, $spfRecord2, $default);
        $this->assertEquals($default, $merged);
    }

    public function testMergeWithInvalidRecords()
    {
        $spfRecord1 = 'invalid-record';
        $spfRecord2 = 'v=spf1 a mx -all';
        $merged = MergeSPF::merge($spfRecord1, $spfRecord2);
        $this->assertEquals('v=spf1 a mx -all', $merged);
    }

    public function testMergeWithTooManyMechanisms()
    {
        $spfRecord1 = 'v=spf1 a mx ip4:192.168.1.1 ip4:192.168.1.2 ip4:192.168.1.3 ip4:192.168.1.4 ip4:192.168.1.5 ip4:192.168.1.6 ip4:192.168.1.7 ip4:192.168.1.8 ip4:192.168.1.9 ip4:192.168.1.10 -all';
        $spfRecord2 = 'v=spf1 ' . str_repeat('a ', 12) . '-all';
        $default = 'v=spf1 -all';
        $merged = MergeSPF::merge($spfRecord1, $spfRecord2, $default);
        $this->assertEquals($default, $merged);
    }

}
