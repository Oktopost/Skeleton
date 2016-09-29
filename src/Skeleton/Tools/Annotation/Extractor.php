<?php
namespace Skeleton\Tools\Annotation;


class Extractor
{
	/**
	 * @param mixed $element
	 * @return string
	 */
	private function getDocComment($element)
	{
		if ($element instanceof \ReflectionClass ||
			$element instanceof \ReflectionMethod ||
			$element instanceof \ReflectionProperty)
		{
			return $element->getDocComment();
		}
		else
		{
			return (new \ReflectionClass($element))->getDocComment();
		}
	}
	
	
	/**
	 * @param mixed $element
	 * @param string|array $annotation
	 * @param bool $allowComment
	 * @return bool
	 */
	public function has($element, $annotation, $allowComment = true)
	{
		if (is_array($annotation))
		{
			foreach ($annotation as $singleAnnotation)
			{
				if ($this->has($element, $singleAnnotation, $allowComment)) 
					return true;
			}
			
			return false;
		}
		
		$pattern = $allowComment ?
			"/^[ \\t]*\\*[ \\t]*@{$annotation}.*$/m" : 
			"/^[ \\t]*\\*[ \\t]*@{$annotation}[ \\t]*$/m";
		
		return (preg_match($pattern, $this->getDocComment($element)) == 1);
	}
	
	/**
	 * @param mixed $element
	 * @param string $annotation
	 * @return bool
	 */
	public function get($element, $annotation)
	{
		$pattern = "/^[ \\t]*\\*[ \\t]*@{$annotation} ([\\w\\\\]*).*$/m";
		$matches = [];
		$result = preg_match($pattern, $this->getDocComment($element), $matches);
		
		return ($result === 1 ?
			$matches[1] : 
			false);
	}
	
	/**
	 * @param mixed $element
	 * @param string $parameterName
	 * @return bool
	 */
	public function getParameterType($element, $parameterName)
	{		
		$pattern = "/^[ \\t]*\\*[ \\t]*@var ([\\w\\\\]+)[ \\t]+\\$?{$parameterName}.*$/m";
		$matches = [];
		$result = preg_match($pattern, $this->getDocComment($element), $matches);
		
		return ($result === 1 ?
			$matches[1] :
			false);
	}
}