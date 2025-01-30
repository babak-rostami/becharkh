<?php

namespace App\Jobs\Item;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class UpdateItemPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $item;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $item = $this->item;
        $client = HttpClient::create();
        $browser = new HttpBrowser($client);
        $url = $item->crl_price_url;
        $crawler = $browser->request('GET', $url);

        $itemNodes = $crawler->filter('.legend .item');
        if ($itemNodes->count() > 0) {
            $price_number = 0;
            $result = [];
            while ($price_number != -1) {
                $price_title = $itemNodes->filter('.title')->eq($price_number);
                if ($price_title->count() > 0) {
                    $title = $price_title->text();
                    $car_trim = $itemNodes->filter('.subtitle .trim-fa')->eq($price_number);
                    if ($car_trim->count() > 0) {
                        $title .= " - " . $car_trim->text();
                    }
                    $car_model_year = $itemNodes->filter('.subtitle .model-year')->eq($price_number);
                    if ($car_model_year->count() > 0) {
                        $title .= " - " . $car_model_year->text();
                    }
                    $car_price = $itemNodes->filter('.price-text')->eq($price_number);
                    $price = $car_price->text();
                    $price_number += 1;
                    $result[$title] = $price;
                } else {
                    $price_number = -1;
                }
            }
            if (count($result) > 0) {
                $result['last_update'] = now()->timestamp;
                $item->prices = $result;
                $item->update();
            }
        }
    }
}
