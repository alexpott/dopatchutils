<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 09/08/2013
 * Time: 02:42
 * To change this template use File | Settings | File Templates.
 */

namespace DrupalPatchUtils\Command;

use DrupalPatchUtils\DoBrowser;
use DrupalPatchUtils\Issue;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class SearchIssuePatch
 * @package DrupalPatchUtils\Command
 */
class IssueStat extends CommandBase {

  protected function configure()
  {
    $this
      ->setName('issueStat')
      ->setAliases(array('istat'))
      ->setDescription('Gets issue statistics');
//      ->addArgument(
//        'nid',
//        InputArgument::REQUIRED,
//        'What is the nid to get stats for?'
//      );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $browser = new DoBrowser();
    $nids = <<<EOF
2753733
1079762
2610436
2207247
2796151
2763787
2694391
2818031
2796581
2721179
2807879
2616756
2784357
2717207
2664530
2767655
2683435
2811927
2811357
2795845
2504849
2807705
2808497
2485385
2807171
2776235
2804675
2567715
2785589
2753939
728702
2783079
2690389
2744197
2239419
2762549
2791163
2485385
2794249
2657978
2785891
2794207
2684873
1267508
2560795
2793091
2787619
2787657
2777451
2490290
2572801
2782009
2771547
2213671
2776357
2787577
2787567
2515050
2740983
2785155
2406533
2784341
2783749
2779939
2780549
2635784
2765437
2780549
2725533
2513534
2553733
2291055
2638140
1869548
1920902
306662
1863898
2750167
88183
2712935
2278383
2702661
2688945
2749955
2754783
2678662
2752413
2329453
2702661
2768953
2747083
2666382
2627678
2333243
2764687
2624822
2718733
2353611
2762549
2761865
2568247
2755843
2757427
2754477
2353611
2471010
2687003
2228141
2499239
2754477
2676552
2648956
2724813
2650812
2308745
2462653
2692247
2575725
2658438
2603074
2226317
2598038
2466197
2713831
2652538
2721355
2646328
2617152
2724225
2605654
2699613
842620
2575519
2676186
2723345
2380293
2721359
2380293
2721283
2572619
2719695
2157963
2715965
2572777
2718583
2693725
2407853
2710201
2707641
2707261
2572307
2582475
2707619
2614202
2711645
2708379
2702281
2572601
2710081
2572707
2709541
2707109
2706669
2707467
2704821
2706113
2706679
2469721
2589967
2703339
2665992
2688535
2337283
2700367
2676836
2469713
1850080
2674408
2697637
2697291
2614824
2664274
2632314
2680931
2664748
2595613
2666650
2692359
2683421
2674408
2682705
2573975
2683579
2469713
2687837
2664274
2469713
2676222
2676258
2674408
2625696
2281393
2674408
1475510
2680057
2683263
2675066
2177335
2670978
2609680
2179537
2623940
2281691
2678770
2678564
2675000
2625696
2676258
2662552
2613878
2625696
2676346
2342015
2496867
2675534
2671034
2579691
2666172
2671708
2642374
2454057
2659100
2620576
2664466
2664302
2664324
2663264
2663410
2487269
2598502
2516930
2666112
2598502
2652068
2572643
2650588
2662990
2571539
2651766
2578173
2489472
2392057
2649892
2642362
2567815
2649748
2479487
2463113
2392153
2643280
2482857
2635584
1559506
2606548
2595695
2461671
2400543
1443308
2636228
2625782
2550519
2625258
2606058
2625216
2623680
2075889
2618886
2382675
2458223
2486467
2458223
2616282
2614014
1919468
2616336
2616846
2615776
1810394
2580293
2609694
2609076
1269780
2581443
2584419
2574975
2603152
2606460
2600672
2606470
2491875
2597628
2603152
2590105
2578377
2598232
2600200
2600282
2600176
2580049
2513266
2596801
2599446
2592665
2527126
2584603
2580389
1006266
2592367
2585165
2581459
2583009
2533800
2570285
2581683
2520526
2580575
2575421
2429191
2487588
2503755
2488568
2503755
2579615
2520540
2557367
1978714
2570895
2503755
2553909
2558885
2575615
2576533
2577785
2567257
2380389
2570431
2571695
2575599
2559971
2572929
2572597
2571909
2571935
2509218
2247291
2568613
2571375
2568775
2570107
2569069
2569281
2568977
2568793
2557113
2569419
2545972
2569293
2568607
2568611
2568609
2560863
2564283
2568013
2506445
2568015
2568027
2566447
2568085
2451397
2565981
2566319
2565971
2562155
2510310
2564353
2565241
2487592
2564321
2538950
1031122
2562487
2560641
2554889
2560751
2561129
2561229
2561121
2560851
2561135
2557023
2560897
2549393
2547581
2559969
2555473
2557467
2560055
2559877
2549921
2551267
2513604
2555931
2557871
2550981
2551511
2542748
2557577
2557519
2553533
2501971
2552579
2550055
2505931
2553953
2338167
2501319
2296929
2553969
2547851
2551725
2549791
2550467
2544932
2504529
2551335
2546232
2304461
2363423
2550945
2501697
2501757
2296929
2501931
2296101
2529188
2384567
2544684
2545990
2545988
2542128
2501835
2501711
2538260
2399261
2546582
2501455
2525908
2503861
2546176
2483357
2542132
2537928
2538228
2514044
2382513
2533822
2539310
2338081
2497275
2525910
2535082
2408013
2536678
2536880
2506581
2533946
2512106
2504141
2532604
2527708
2532434
2506195
2505989
2526458
2527486
2453175
2513646
2474363
2309215
2477853
2443679
2408371
2512452
2491987
2508666
2506349
2508591
2506133
2504417
2497323
2410019
2475221
2451359
2497259
2384675
2392293
2494989
2502477
2381277
2195573
2460529
2389811
2254865
2429443
2482231
2495657
2495833
2475483
753898
2357997
2486475
2361423
2337753
2456025
2473907
2488954
2416109
2489100
2369987
2383865
2247379
2482215
2411689
2430219
2432791
2204697
2443651
2395143
2458387
2474909
2450251
2405165
2350569
2476247
2395143
2473075
2474567
2474835
2457551
2331783
2395143
2297817
2459753
2417549
2232861
2459155
2463817
2465611
2449445
2451363
2460847
2463821
2457695
2457887
2458925
2411689
2454859
2090115
2452347
2449445
2426495
2436835
2315015
2406681
2448213
2448223
2315791
2424445
2388941
2347625
2433009
2416409
2421263
1013034
1853856
2413753
2350933
2388867
2383165
2426457
2420107
2426533
2401919
2229145
2422745
2423781
2422019
2421335
1164784
2406103
2419005
2419857
2361775
2415645
2349625
2410151
2414991
2414953
2030607
2409811
2388749
2412373
2405213
2368767
2221699
2406543
2350551
2303391
2140511
2392319
2405465
2030571
2403117
2388125
1907170
2392787
2403169
2403101
2397681
2397297
2401035
2397691
2392351
2395395
2395511
2395515
2393765
2170235
2392281
1985406
2183983
2390749
2390615
2388765
2335879
2355909
2387443
2385545
2384853
2357145
2381909
2383307
2235901
2377397
1002164
2348925
2374125
2316909
2030613
2350821
2376039
2377393
2372323
2376147
2368349
2374087
2321385
2364647
1426804
2371843
2267453
2236855
2346287
2366877
1879930
2271419
2356297
2364173
2052751
2221577
2362519
2282519
1956698
2224581
2329767
2359005
2226533
1825466
2332935
2356845
2205527
2355545
2235363
2353347
2302799
2340667
2352641
2310093
2313883
2070737
2300131
2350917
2195957
2338759
2329501
2277103
2328161
2343213
2340251
2343607
2261425
2336355
1860594
2339435
2337619
2312093
2232605
2337847
2287071
2260457
2334763
2336177
2148199
2336689
2148199
2333907
2022875
2324121
2319159
2332739
2248767
2202185
2224761
2303521
2272879
2313875
2303881
2326203
2271529
2251113
2283977
2319671
2217755
2317865
2317881
2315019
2306539
2314347
1285726
2263365
2301317
2295129
2306455
2279395
2293773
2287727
2295737
2284103
2196241
2301493
2294177
2030655
2242405
2076325
2239969
2299457
2298993
2234159
2293773
1452896
2295571
2291721
2225485
2272987
2247095
2144263
2286681
2016629
2291777
2143291
2287139
2005434
2284111
2287193
2114823
2256679
2267159
2228763
2269323
2278025
2081153
2280501
2244447
2016629
1848266
2276571
2271967
2276183
2236781
2253109
2226301
2261131
2224887
2100369
1289536
2264179
2207777
2262861
2266547
2254495
2259301
2263287
2263273
2263255
2263201
2259525
2261677
2228261
2245727
2254495
2176621
EOF;
    $nids = explode("\n", $nids);
    foreach ($nids as $nid) {
      $url = 'https://www.drupal.org/node/' . $nid;
      if ($issue = $this->getIssue($url, $browser)) {
        $this->getStats($nid, $issue, $browser, $input, $output);
      }
    }
  }

  protected function getStats($nid, Issue $issue, DoBrowser $browser, InputInterface $input, OutputInterface $output)
  {
    $priority = $issue->getCrawler()
      ->filter('.field-name-field-issue-priority .field-item')
      ->text();
    $output->writeln($nid . ': ' . $priority);
  }
}