<?php

/*
 * This file is part of the proprietary project.
 *
 * This file and its contents are confidential and protected by copyright law.
 * Unauthorized copying, distribution, or disclosure of this content
 * is strictly prohibited without prior written consent from the author or
 * copyright owner.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Commands;

use App\Models\FeedItem;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\I18n\Time;
use Feed;

class LwnAlert extends BaseCommand
{
    /**
     * The Command's Group.
     *
     * @var string
     */
    protected $group = 'cron';

    /**
     * The Command's Name.
     *
     * @var string
     */
    protected $name = 'cron:lwn';

    /**
     * The Command's Description.
     *
     * @var string
     */
    protected $description = 'Sends notification when LWN article is open';

    /**
     * The Command's Usage.
     *
     * @var string
     */
    protected $usage = 'command:name [arguments] [options]';

    /**
     * The Command's Arguments.
     *
     * @var array<string, string>
     */
    protected $arguments = [];

    /**
     * The Command's Options.
     *
     * @var array<string, string>
     */
    protected $options = [];

    /**
     * This command insert entries from LWN RSS feed into the database. This
     * job runs every day and looks for articles are 14 to 16 days old (2 days
     * interval). This is to avoid not missing emails if timezone errors or
     * email failures happen.
     *
     * The sender must ensure that we don't send duplicate emails next day.
     */
    public function run(array $params): void
    {
        log_message('info', 'Running with parameter . '.json_encode($params));
        $this->updateRssEntries();

        // now find entries that are now open and needs to be notified about.
        $tillHours = (- 14 * 24); // 2 weeks.
        // $tillHours = (- 24);
        $sinceHours =  $tillHours - 24; // 1 day window.

        $items = model(FeedItem::class)
            ->where('publication_date <', Time::parse($tillHours.' hours'))
            ->where('publication_date >=', Time::parse($sinceHours.' hours'))
            ->findAll();

        // Notify devs about triggered job.
        service('smtp')->notifyDevs(
            '<code><pre>'.json_encode($items).'</pre></code>',
            subject: self::class.':'.__FUNCTION__.' ran at '.Time::now()
        );

        log_message('info', 'Total '.count($items).' articles are open');
        foreach ($items as $item) {
            $this->sendEmailToSubscriber($item);
        }
    }

    /**
     * @param array<string, string> $item
     */
    private function sendEmailToSubscriber(array $item): void
    {
        log_message('info', 'Notifying subscribers about article '.json_encode($item));
        $groupEmail = 'maxflow-lwn-notification@googlegroups.com';

        // TODO: Get list of activce subscriber. Currently, send email to dev
        // only.
        $subscribers = [$groupEmail];
        foreach ($subscribers as $subscriber) {
            service('smtp')->sendLwnEmailArticleNotBehindPaywall($subscriber, article: $item);
        }
    }

    private function updateRssEntries(): void
    {
        \Feed::$cacheDir = sys_get_temp_dir();

        // The upstream has wrong type.
        // @phpstan-ignore assign.propertyType
        \Feed::$cacheExpire = '12 hours';

        $feedSource = 'https://lwn.net/headlines/rss';
        $feed = \Feed::loadRss($feedSource);

        foreach ($feed->item as $item) {
            $item = (array) $item;

            $item['feed_source'] = $feedSource;

            $pubDate = Time::parse($item['pubDate']);

            $item['publication_date'] = $pubDate;
            $item['timestamp'] = $pubDate->timestamp;
            $item['author'] = $item['dc:creator'];

            $feedItemModel = model(FeedItem::class);
            $existingItem = (array) $feedItemModel
                ->where('feed_source', $feedSource)->where('guid', $item['guid'])->first();

            if ([] === $existingItem) {
                log_message('debug', 'Inserting new item '.json_encode($item));
                $feedItemModel->insert($item);
            } else {
                log_message('debug', 'Updating new item '.json_encode($item));
                $feedItemModel->update($existingItem['id'], $item);
            }
        }
    }
}
