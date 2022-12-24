<?php

namespace App\Entity;

use App\Repository\ArticleOptionRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=ArticleOptionRepository::class)
 *  * @ApiResource(
 *  normalizationContext={"groups"={"article_option:list"}},
 *  collectionOperations={"get"},
 *  itemOperations={"get"}
 * )
 */
class ArticleOption
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"article_option:list","article:list"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"article_option:list","article:list"})
     */
    private $content;


    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="options")
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = ucfirst($title);

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = ucfirst($content);

        return $this;
    }


    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }
}