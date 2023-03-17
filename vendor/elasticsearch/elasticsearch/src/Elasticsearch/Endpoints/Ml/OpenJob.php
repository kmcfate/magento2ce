<?php
/**
 * Elasticsearch PHP client
 *
 * @link      https://github.com/elastic/elasticsearch-php/
 * @copyright Copyright (c) Elasticsearch B.V (https://www.elastic.co)
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license   https://www.gnu.org/licenses/lgpl-2.1.html GNU Lesser General Public License, Version 2.1 
 * 
 * Licensed to Elasticsearch B.V under one or more agreements.
 * Elasticsearch B.V licenses this file to you under the Apache 2.0 License or
 * the GNU Lesser General Public License, Version 2.1, at your option.
 * See the LICENSE file in the project root for more information.
 */
declare(strict_types = 1);

namespace Elasticsearch\Endpoints\Ml;

use Elasticsearch\Common\Exceptions\RuntimeException;
use Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class OpenJob
 * Elasticsearch API name xpack.ml.open_job
 *
 * NOTE: this file is autogenerated using util/GenerateEndpoints.php
 * and Elasticsearch 6.8.16 (1f62092)
 */
class OpenJob extends AbstractEndpoint
{
    protected $job_id;
    protected $ignore_downtime;
    protected $timeout;

    public function getURI(): string
    {
        if (isset($this->job_id) !== true) {
            throw new RuntimeException(
                'job_id is required for Open_job'
            );
        }
        $job_id = $this->job_id;
        $ignore_downtime = $this->ignore_downtime ?? null;
        $timeout = $this->timeout ?? null;

        return "/_xpack/ml/anomaly_detectors/$job_id/_open";
    }

    public function getParamWhitelist(): array
    {
        return [];
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function setJobId($job_id): OpenJob
    {
        if (isset($job_id) !== true) {
            return $this;
        }
        $this->job_id = $job_id;

        return $this;
    }

    public function setIgnoreDowntime($ignore_downtime): OpenJob
    {
        if (isset($ignore_downtime) !== true) {
            return $this;
        }
        $this->ignore_downtime = $ignore_downtime;

        return $this;
    }

    public function setTimeout($timeout): OpenJob
    {
        if (isset($timeout) !== true) {
            return $this;
        }
        $this->timeout = $timeout;

        return $this;
    }
}
