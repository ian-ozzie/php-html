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
          php = pkgs.php83;
        in
        {
          default = mkShell {
            packages = with pkgs; [
              php
              php.packages.composer
              pre-commit
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
