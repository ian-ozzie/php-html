{
  description = "Object based HTML generator";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
  };

  outputs =
    { nixpkgs, ... }:
    let
      systems = [
        "x86_64-linux"
        "aarch64-darwin"
      ];
    in
    {
      devShells = nixpkgs.lib.genAttrs systems (
        system:
        let
          inherit (nixpkgs.legacyPackages.${system}) mkShell;
          pkgs = nixpkgs.legacyPackages.${system};
          customPHP = pkgs.php83.buildEnv {
            extensions = { enabled, all, ... }:
              enabled
              ++ (with all; [
                pcov
                xdebug
              ]);
            extraConfig = ''
              error_reporting = E_ALL
              display_errors = On
              display_startup_errors = On
              log_errors = On
              log_errors_max_len = 0

              [xdebug]
              xdebug.mode = develop,debug
              xdebug.discover_client_host = 1
              xdebug.start_with_request = trigger
              xdebug.log_level = 0
            '';
          };
        in
        {
          default = mkShell {
            packages = with pkgs; [
              customPHP
              customPHP.packages.composer
              pre-commit
              xc
            ];

            shellHook = ''
              echo "PHP $(php --version | head -1)"
              echo "Composer $(composer --version)"

              pre-commit install
            '';
          };
        }
      );
    };
}
