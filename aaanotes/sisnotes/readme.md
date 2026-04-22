# SIS Development Ideas

These are just some strategies I use to help me when developing for SIS. Maybe they can help us all?

## Interpreting the PR Requirements

Read the requirements and be sure you fully understand what is required. Be sure that you recognise the intent of every word in the spec.
Sometimes I need to read the spec a number of times and even look up the meaning of words to get the full intent of what's being asked for.
If you still think it's a little ambiguous, ask for clarification or use Claude to help get a better understanding.

Checklist:
- Re-read the spec again until it clicks
- Take notes in a .md file of what you currently understand and outline what is a little fuzzy
- Ask Claude to help
- Ask another developer to help clarify
- Ask for clarification from the story author

I find the requirements are generally written in a way that requires more focus than a general requirement that you would receive from a non-technical client.

Another thing to keep in mind, and something I struggle with, is to stop and ask yourself, "Can this task be logically broken down into sub-tasks?" If yes, then add that to the story outlining the smaller sub-tasks and possibly a story for each sub-task.


## While You Wait...

Sometimes you will find that you are waiting for a response or review of a PR. This is a good time to review the stories and have one picked out you can be reading while you wait. I will generally have multiple uncommitted branches on the go, experimenting with ideas or implementations that generally never make it to a PR. I like to have a totally external project such as this repo `tk-app` as just a place to implement things for the future that does not impact SIS at all.    

You can spend the free time looking into SISv1 to see if it has any similarities to what you are trying to do.


## Publishing a PR

We have to be careful when publishing a PR for review.
The main goal here is to ensure the reviewers and devs are not overwhelmed with PRs that are not ready to be merged.
Try to focus your attention on one PR at a time. 
However, do have multiple braches and issues you are working on in the background, try not to be a machine and publish multiple PRs at once, that may come back to bite you. 
This is not a hard and fast rule, you may find at times you have to publish multiple PRs, but they should be relatively small and focused.

The PR description should have the issue ID number and the major changes along with a **current** test procedure. If you have refactored the code, be sure to review the test procedure and make sure it still works. This takes time but is important to those reviewing the PR.

When I publish a PR, I use a template that was used in SISv1, I added some notes to remind me of what to check before publishing. 
I usually work on this as I work on the PR to keep track of what I have done. When I feel the code is ready to publish, the PR test procedure is generally ready to go to. 

[Here is my version of the PR template](./pr_template.md)


**HOT TIP:**

I add the PR template to the sisv2 `./_notes/pr-notes/_template.md` and use a bash script `./_notes/pr-notes/new-pr.sh` that I can run to generate a new pr document from the branch name. (eg: `sc-123-start-a-pr`)
```bash
#!/bin/bash -e
#
# create a new PR doc from the template
#

SCRIPT=$(realpath "$0")
SCRIPT_PATH=$(dirname "$SCRIPT")
BRANCH=$(git rev-parse --abbrev-ref HEAD)

cd "$SCRIPT_PATH"

if [[ ! -f "$SCRIPT_PATH/$BRANCH.md" ]]; then
  cp "$SCRIPT_PATH/_template.md" "$SCRIPT_PATH/$BRANCH.md"

  escaped_branch=$(printf '%s\n' "$BRANCH" | sed 's/[&/\]/\\&/g')
  tmp_file=$(mktemp)
  sed "s/{branch}/$escaped_branch/g" "$SCRIPT_PATH/$BRANCH.md" > "$tmp_file" && mv "$tmp_file" "$SCRIPT_PATH/$BRANCH.md"

  sc_prefix=$(printf '%s\n' "$BRANCH" | sed -n 's/^\(sc-[0-9][0-9]*\)-.*/\1/p')
  escaped_sc_prefix=$(printf '%s\n' "$sc_prefix" | sed 's/[&/\]/\\&/g')
  tmp_file=$(mktemp)
  sed "s/{sc-nnn}/$escaped_sc_prefix/g" "$SCRIPT_PATH/$BRANCH.md" > "$tmp_file" && mv "$tmp_file" "$SCRIPT_PATH/$BRANCH.md"

  echo "New PR template created $BRANCH.md"
else
  echo "PR template $BRANCH.md already exists"
fi
```
If you're using PhpStorm and can execute bash scripts, right-click on this file and select `Run` to create a new document.


# Handy References

- [PR template](./pr_template.md)
- [SIS Acronyms](./acronyms.md)

