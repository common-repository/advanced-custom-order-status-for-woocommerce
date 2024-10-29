<?php

namespace StorePlugin\CustomOrderStatus\Abstracts;

use ReflectionClass;
use ReflectionException;
use Elementor\Widget_Base;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use StorePlugin\CustomOrderStatus\Container\Dice;
use StorePlugin\CustomOrderStatus\Contracts\ServiceInterface;

/**
 * Autowire class is responsible to run all classes automagically
 *
 * @package StorePlugin\CustomOrderStatus\Abstracts
 */
abstract class Autowire {

	/**
	 * Get psr-4 prefixes from Composer's ClassLoader.
	 *
	 * @var array
	 */
    private array $psr4Prefixes;

	/**
	 * Set namespace from your project composer.json
	 *
	 * @var string
	 */
    private string $namespace;

	/**
	 * Dice container instance.
	 *
	 * @var Dice
	 */
	protected $container;

	/**
	 * Constructs object and inserts prefixes from composer.
	 *
	 * @param array Composer's Psr4Prefixes.
	 * @param string Plugin namespace.
	 */
	public function __construct( array $psr4Prefixes, string $pluginNamespace )
	{
		$this->psr4Prefixes = $psr4Prefixes;
		$this->namespace = $pluginNamespace;
	}

	/**
	 * Set dice object as dependency injection
	 *
	 * @param Dice $container
	 * @return Dice
	 */
	abstract public function container( Dice $container ): Autowire;

	/**
	 * Register every service with Dice dependency injection.
	 *
	 * @return void
	 */
	public function register(): void
	{
		$classNames = $this->fetchClassesInNamespace(
			$this->namespace,
			$this->psr4Prefixes
		);

		if( is_array( $classNames ) ) {
			foreach ( $classNames as $fileName => $classList ) {
				if ( $fileName === 'Widgets' ) {
					$this->registerWidgets( $classList );
				} else {
					$this->processClass( $classList );
				}
			}
		}
	}

	/**
	 * Process and build a ReflectionClass for autowiring
	 *
	 * @param array $classList
	 * @return void
	 */
	private function processClass( $classList ): void
	{
		if( is_array( $classList ) ) {
			foreach ( $classList as $class ) {
				try {
					$reflClass = new ReflectionClass( $class );

					if (
						$reflClass->isAbstract() ||
						$reflClass->isInterface() ||
						$reflClass->isTrait() ||
						! ( $reflClass->implementsInterface( ServiceInterface::class ) )
					) {
						continue;
					}

					// Make sure every object has instantiated once
					$container = $this->container->addRules([
						$class => [
							'shared' => true,
							'call'	=> [
								['register', []],
							]
						]
					]);

					$container->create( $class );

				} catch ( ReflectionException $e ) {
					$this->handleReflectionException( $class );
				}
			}
		}
	}

	/**
	 * Register widget classes automagically from Widgets directory
	 *
	 * @param array $classList
	 * @return void
	 */
	private function registerWidgets( array $classList ): void
	{
		add_action( 'elementor/widgets/register', function( $widgets_manager ) use ( $classList ) {
			if( is_array( $classList ) ) {
				foreach ( $classList as $class ) {
					try {
						$reflClass = new ReflectionClass( $class );
						if ( $reflClass->isSubclassOf( Widget_Base::class ) ) {
							// Make sure every object has instantiated once
							$container = $this->container->addRules( [ $class => [ 'shared' => true ] ] );
							$widgets_manager->register( $container->create( $class ) );
						}
					} catch ( ReflectionException $e ) {
						$this->handleReflectionException( $class );
					}
				}
			}
		});
	}

	/**
	 * Show exception error message for ReflectionException
	 *
	 * @param string $class
	 * @return void
	 */
	private function handleReflectionException( string $class ): void
	{
		printf(
			'Unable to reflect class %s. Please check if the namespace is PSR-4 compliant.',
			esc_html( $class )
		);
	}

    /**
	 * Prepare PSR-4 ready namespace from file's path.
	 *
	 * @param string $filepath
	 * @param string $rootNamespace
	 * @param string $rootNamespacePath
	 * @return string
	 */
	private function parseNamespaceFromFilepath( string $filepath, string $rootNamespace, string $rootNamespacePath ): string
	{
		$pathNamespace = \str_replace(
			[$rootNamespacePath, \DIRECTORY_SEPARATOR, '.php'],
			['', '\\', ''],
			$filepath
		);

		return rtrim( $rootNamespace, '\\' ) . $pathNamespace;
	}

    /**
	 * Returns all classes in namespace.
	 *
	 * @param string $namespaceName
	 * @param array $psr4Prefixes Array of psr-4 namespaces and directories.
	 * @return string[]
	 */
	private function fetchClassesInNamespace( string $namespaceName, array $psr4Prefixes ): array
	{
		$classes = [];
		$namespaceWithSlash = "{$namespaceName}\\";
		$pathToNamespace = $psr4Prefixes[$namespaceWithSlash][0] ?? '';

		if ( ! \is_dir( $pathToNamespace ) ) {
			return [];
		}

		$dirIterate = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $pathToNamespace ) );
		foreach ( $dirIterate as $file ) {
			if ( $file->isDir() || ! \preg_match( '/^[A-Z]{1}[A-Za-z0-9]+\.php$/', $file->getFileName() ) ) {
				continue;
			}

			$pathName = explode( '/', rtrim( str_replace( '\\', '/', $file->getPathname() ) ) );
			$directoryName = array_slice( $pathName, -2, 1 );
			$classes[ $directoryName[0] ][] = $this->parseNamespaceFromFilepath( $file->getPathname(), $namespaceName, $pathToNamespace );
		}

		return $classes;
	}

}
