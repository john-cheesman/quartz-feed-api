<?php
// Routes

use PicoFeed\Reader\Reader;
use PicoFeed\PicoFeedException;
use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/getFeed', function (Request $request, Response $response) {
    try {
        $url = $request->getQueryParam('url');
        $reader = new Reader;
        $resource = $reader->download($url);

        if ($resource) {
            $parser = $reader->getParser(
                $resource->getUrl(),
                $resource->getContent(),
                $resource->getEncoding()
            );

            $feed = $parser->execute();
            $response->withStatus(200);
            $response->withHeader('Content-Type', 'application/json');
            echo json_encode($feed);
        } else {
            throw new PDOException('No feed data', 101);
        }
    } catch (PicoFeedException $e) {
        $response->withStatus(404);
        $this->logger->info('Error: ' . $e->getMessage() . ' Code: ' . $e->getCode());
        echo '{"error":{"text":' . $e->getMessage() . ', "code":' . $e->getCode() . '}}';
    }
});

$app->get('/getAvailableFeeds', function (Request $request, Response $response) {
    try {
        $url = $request->getQueryParam('url');
        $reader = new Reader;
        $resource = $reader->download($url);

        if ($resource) {
            $feeds = $reader->find(
                $resource->getUrl(),
                $resource->getContent()
            );

            echo json_encode($feeds);
        } else {
            throw new PDOException('No available feeds', 102);
        }
    } catch (PicoFeedException $e) {
        $response->withStatus(404);
        $this->logger->info('Error: ' . $e->getMessage() . ' Code: ' . $e->getCode());
        echo '{"error":{"text":' . $e->getMessage() . ', "code":' . $e->getCode() . '}}';
    }
});

