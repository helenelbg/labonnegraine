Process service
=====================================


Allow one or multiple processes to be run in a structured way. This can be used for Server Sent Events (SSE) for example.


## USAGE

1. To use a method from MyClass add the ProcessInterface and needed Trait


```php
class MyClass implements ProcessInterface
{
use ProcessTrait; // or  ProcessWithPaginationTrait

    public function myMethod($arg1, $arg2){
    
    }
...
} 
```
2. Instantiate a process with ```$MyClass``` as parameter, and set method and eventually the method arguments 

```php
$myProcess = new Process(ProcessInterface $MyClass);
$myProcess->setMethod('myMethod');
    // if arguments needed, set it (sorted)in method order as array
    ->setMethodArguments([$arg1, $arg2])
...
```

3. Run the process

```php
...
$myProcess->run()
```

### Multiple Process

1. Instantiate a processCollection and

```php
$myProcess = new Process(ProcessInterface $MyClass);
$myProcess->setMethod('myMethod');
    // if arguments needed, set it in method order as array
    ->setMethodArguments([$arg1, $arg2])
```

2. add method stubs from Interface and customize


### Using with SSE