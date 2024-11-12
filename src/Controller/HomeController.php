<?php

namespace App\Controller;

use App\Form\GetImageType;
use DOMDocument;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/test', name: 'app_test', methods: ['GET'])]
    public function test(): Response
    {
        return new Response(
            '<html><body><h1>Test url<h1></body></html>'
        );
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(GetImageType::class, [
            'url' => 'https://mayak.travel',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('app_home');
        }

        $images = [];

        return $this->render('home/url.html.twig', [
            'filter' => $form->createView(),
            'images' => $images,
        ]);
    }

    #[Route('/url', name: 'app_url', methods: ['POST'] )]
    public function show(Request $request): Response
    {
        $url = json_decode($request->getContent())->url;

        try {
            $images = [];
            $html = file_get_contents($url??'');

            //https://ru.freepik.com/popular-photos
            $pattern = '/(https?:\/\/[^\s]+?\.(jpeg|jpg|svg|png))/i';
            preg_match_all($pattern, $html, $matches);
            $allUrls = $matches[1];

            foreach ($allUrls as $url) {
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $images[] = $url;
                }
            }

            //https://yandex.ru/images/
            $pattern = '/<img[^>]+src=["\']?([^"\'>]+)["\']?/';
            preg_match_all($pattern, $html, $matches);
            $yandexUrls = $matches[1];

//        dd($yandexUrls);
            $cnt = count($yandexUrls);
            foreach ($yandexUrls as $key => $value) {
                if ($key != $cnt) {
                    $ampPos = strpos($value, '&amp;');
                    // Если &amp; найден, обрезаем строку
                    if ($ampPos !== false) {
                        $value = substr($value, 0, $ampPos);
                    }
                    if (filter_var($value, FILTER_VALIDATE_URL)) {
                        $images[] = $value;
                    }

                }
            }

            $dom = new DOMDocument();
            @$dom->loadHTML($html);

            // Поиск изображений в тегах <img>
            foreach ($dom->getElementsByTagName('img') as $img) {
                $src = $img->getAttribute('src');
                if (preg_match('/\.(jpeg|jpg|png|svg)$/i', $src)) {
                    $images[] = $this->getAbsoluteUrl($src, $url);
                }
            }

            // Поиск изображений в стилях background-image
            foreach ($dom->getElementsByTagName('*') as $element) {
                $style = $element->getAttribute('style');
                if (preg_match('/background-image:\s*url\((["\']?)(.*?)\1\)/', $style, $matches)) {
                    $imageUrl = $matches[2];
                    if (preg_match('/\.(jpeg|jpg|png|svg)$/i', $imageUrl)) {
                        $images[] = $this->getAbsoluteUrl($imageUrl, $url);
                    }
                }
            }

            // Удаляем дубликаты
            $images = array_unique($images);

            $imageData = [];
            $totalSize = 0;
            $count = count($images);
            foreach ($images as $imagePath) {
                $headers = get_headers($imagePath, 1);

                if (isset($headers['Content-Length'])) {
                    $imageSize = $headers['Content-Length'];
                }

                $fileSizeInMB = $this->bytesToMegabytes($imageSize??0.00);

                $totalSize += $fileSizeInMB;
                $imageData[] = [
                    'path' => $imagePath,
                    'size' => $fileSizeInMB,
                ];
            }

        } catch (\Exception $e){
            var_dump($e->getMessage());
        }

        return $this->render('home/index.html.twig', [
            'images' => $imageData??'',
            'total' => $totalSize??0,
            'count' => $count??0,
        ]);
    }

    function bytesToMegabytes($bytes): float|int
    {
        return round($bytes / 1048576, 2); // Делим на 1024 * 1024
    }

    #[Route('/error', name: 'app_error', methods: ['POST'] )]
    public function error(Request $request): Response
    {
        $url = json_decode($request->getContent())->url;

        return $this->render('home/error.html.twig', [
            'message' => 'Неверный url -> ' . $url
        ]);
    }

    private function getAbsoluteUrl($relativeUrl, $baseUrl): string
    {
        // Если URL уже абсолютный, возвращаем его
        if (filter_var($relativeUrl, FILTER_VALIDATE_URL)) {
            return $relativeUrl;
        }

        // Если URL относительный, создаем абсолютный URL
        return rtrim($baseUrl, '/') . '/' . ltrim($relativeUrl, '/');
    }


    // For Tests

}


