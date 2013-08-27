# Usage

## Install
1. Clone the repo
2. Run composer update (see <http://getcomposer.org>)
3. Configure it!

```
./dop configure
```
This will prompt the user to provide a path to a valid drupal check out.

## Commands

### validateRtbcPatches


```
./dop validateRtbcPatches
```
Applies all the latest patches in the RTBC queue to the Drupal repository configured using the configure command. If a patch does not apply it is listed once everything has been checked.

### searchRtbcPatches

```
./dop -vvv searchRtbcPatches PathBasedGeneratorInterface
```
Searches all rtbc patches for "PathBasedGeneratorInterface"


```
./dop -vvv searchRtbcPatches "/PathBased.*Interface/" --regex
```
Searches all rtbc patches using a regex
