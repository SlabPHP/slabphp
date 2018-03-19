# History and More Info

This framework was started in early 2009 under a different name. Some of the ideas presented are obselete but others are pretty neat so it's being reformatted for public consumption. This is mostly an academic exercise as we've already internally switched to other technologies. Some of the interface patterns were designed by Michael Venezia, a friend and mentor to the author of this library. The original author is Eric Salerno of Salerno Labs LLC.

It is known by the authors of this library that inheritance is sometimes frowned-upon and the use of protected values without proper encapsulation is considered bad design. It is also known that some of the methods in this library may be considered anti-patterns. Again, this is mostly being released as a knowledge sharing exercise.

We will most likely continue to keep it updated and maintained but it does depend on reception/feedback.

## Patterns

SlabPHP is built around several patterns that govern and guide the development of SlabPHP applications. It is not meant as a RAD style framework and actually tends more towards the verbose. This is to hopefully promote re-use of code to increase integrity of the code written.

### Pattern One - Polymorphic Hierarchy

Using the bundle stack's ->findClass() functionality, you can search the stack of bundle namespaces to find a class that may override one from something that may not expect it like a shared bundle. This is for flexibility for multi-site implementations but is not generally used for component level class overrides.

See the [Bundle Stack](https://github.com/SlabPHP/bundle-stack) library documentation for more information. 

### Pattern Two - Sequential Controller Execution

Controllers don't need to work this way however it was intended that they are built using three different sequences. The input, operation, and output call queues. The purpose of this is to aid re-use by allowing child controllers to add to or modify methods of a parent controller while maintaing the overall order of operations.

See the [Sequencer library](https://github.com/SlabPHP/sequencer) documentation for more information.

### Pattern Three - Bundles

SlabPHP was originally built to serve multiple sites from one codebase allowing each site to re-use code from other ones. Each site is basically a "bundle". During boot-up you define what bundles should get pushed onto the stack for specific domains. This allows you to deliver your site code via composer as a bundle. See the [Bundle Stack](https://github.com/SlabPHP/bundle-stack) library for more information.