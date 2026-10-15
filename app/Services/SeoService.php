<?php

namespace App\Services;

class SeoService
{
    protected $title;
    protected $description;
    protected $keywords;
    protected $image;
    protected $url;
    protected $type;
    protected $siteName;

    public function __construct()
    {
        $this->siteName = config('app.name', 'Ma\'had Aly Ma\'al Amin');
        $this->type = 'website';
        $this->url = url()->current();
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setKeywords($keywords)
    {
        if (is_array($keywords)) {
            $this->keywords = implode(', ', $keywords);
        } else {
            $this->keywords = $keywords;
        }
        return $this;
    }

    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getTitle()
    {
        if ($this->title) {
            return $this->title . ' - ' . $this->siteName;
        }
        return $this->siteName;
    }

    public function getDescription()
    {
        return $this->description ?: 'Ma\'had Aly Ma\'al Amin - Perguruan Tinggi Islam yang menyelenggarakan pendidikan berkualitas dengan nilai-nilai keislaman.';
    }

    public function getKeywords()
    {
        return $this->keywords ?: 'Ma\'had Aly Ma\'al Amin, perguruan tinggi islam, pendidikan islam, kuliah islam, universitas islam';
    }

    public function getImage()
    {
        return $this->image ?: asset('images/logo-og.png');
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSiteName()
    {
        return $this->siteName;
    }

    public function generateMetaTags()
    {
        $tags = [];
        
        // Basic meta tags
        $tags[] = '<meta name="description" content="' . htmlspecialchars($this->getDescription()) . '">';
        $tags[] = '<meta name="keywords" content="' . htmlspecialchars($this->getKeywords()) . '">';
        $tags[] = '<meta name="author" content="' . htmlspecialchars($this->getSiteName()) . '">';
        $tags[] = '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">';
        $tags[] = '<meta name="googlebot" content="index, follow">';
        
        // Open Graph tags
        $tags[] = '<meta property="og:title" content="' . htmlspecialchars($this->getTitle()) . '">';
        $tags[] = '<meta property="og:description" content="' . htmlspecialchars($this->getDescription()) . '">';
        $tags[] = '<meta property="og:image" content="' . htmlspecialchars($this->getImage()) . '">';
        $tags[] = '<meta property="og:url" content="' . htmlspecialchars($this->getUrl()) . '">';
        $tags[] = '<meta property="og:type" content="' . htmlspecialchars($this->getType()) . '">';
        $tags[] = '<meta property="og:site_name" content="' . htmlspecialchars($this->getSiteName()) . '">';
        $tags[] = '<meta property="og:locale" content="id_ID">';
        
        // Twitter Card tags
        $tags[] = '<meta name="twitter:card" content="summary_large_image">';
        $tags[] = '<meta name="twitter:title" content="' . htmlspecialchars($this->getTitle()) . '">';
        $tags[] = '<meta name="twitter:description" content="' . htmlspecialchars($this->getDescription()) . '">';
        $tags[] = '<meta name="twitter:image" content="' . htmlspecialchars($this->getImage()) . '">';
        
        // Additional SEO meta tags
        $tags[] = '<meta name="theme-color" content="#0d9488">';
        $tags[] = '<meta name="msapplication-TileColor" content="#0d9488">';
        
        // Canonical URL
        $tags[] = '<link rel="canonical" href="' . htmlspecialchars($this->getUrl()) . '">';
        
        return implode("\n    ", $tags);
    }

    public function generateStructuredData($type = 'Organization', $additionalData = [])
    {
        $baseData = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $this->getSiteName(),
            'url' => config('app.url', 'https://maalamin.ac.id'),
            'description' => $this->getDescription(),
            'image' => $this->getImage()
        ];

        if ($type === 'Organization') {
            $baseData = array_merge($baseData, [
                '@type' => 'EducationalOrganization',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressCountry' => 'ID',
                    'addressLocality' => 'Indonesia'
                ],
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'contactType' => 'customer service',
                    'url' => config('app.url', 'https://maalamin.ac.id') . '/kontak'
                ],
                'sameAs' => [
                    'https://www.facebook.com/maalamin',
                    'https://www.instagram.com/maalamin',
                    'https://www.youtube.com/maalamin'
                ]
            ]);
        }

        $structuredData = array_merge($baseData, $additionalData);

        return '<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }

    public function generateBreadcrumbStructuredData($breadcrumbs)
    {
        $breadcrumbList = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];

        foreach ($breadcrumbs as $index => $breadcrumb) {
            $breadcrumbList['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url']
            ];
        }

        return '<script type="application/ld+json">' . json_encode($breadcrumbList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}
