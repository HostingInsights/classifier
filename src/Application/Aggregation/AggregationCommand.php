<?php

namespace Startwind\WebInsights\Application\Aggregation;

use Startwind\WebInsights\Aggregation\Configuration\AggregationConfiguration;
use Startwind\WebInsights\Util\Timer;
use Symfony\Component\Console\Command\Command;

abstract class AggregationCommand extends Command
{
    protected const OPTION_CONFIG_FILE = 'configFile';

    protected AggregationConfiguration $configuration;

    protected function doExecute(): int
    {
        $retriever = $this->configuration->getRetriever();

        $aggregators = $this->configuration->getAggregators();

        $count = 0;

        $aggregationTimer = new Timer();
        $nextTimer = new Timer();

        while ($classificationResult = $retriever->next()) {
            $nextTime = $nextTimer->getTimePassed();
            if ($nextTime > 1000) {
                $this->configuration->getLogger()->warning('Next was slow. Time: ' . $nextTime . ' ms.');
            }
            $count++;
            foreach ($aggregators as $aggregator) {
                $aggregationTimer->start();
                $aggregator->aggregate($classificationResult);
                $time = $aggregationTimer->getTimePassed(Timer::UNIT_MILLISECONDS);
                if ($time > 10) {
                    $this->configuration->getLogger()->warning('Aggregation was slow. Aggregator: ' . get_class($aggregator) . ', time: ' . $time . ' micro seconds.');
                }
            }

            if ($count % 5000 == 0) {
                $this->configuration->getLogger()->warning('Count: ' . $count);
            }

            $nextTimer->start();
        }

        if ($count === 0) {
            throw new \RuntimeException('The given builder query did not return any results.');
        }

        $exporter = $this->configuration->getExporter();

        foreach ($aggregators as $aggregator) {
            $exporter->export($aggregator->finish());
        }

        $exporter->finish($count);

        $this->configuration->getLogger()->info('Aggregation finished. Memory usage: ' . (int)(memory_get_peak_usage() / 1024 / 1024) . ' MB.');

        return $count;
    }
}
